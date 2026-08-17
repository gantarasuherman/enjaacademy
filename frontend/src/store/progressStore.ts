import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import type { Bookmark, DailyActivity, Progress, UnlockedAchievement } from '@/types';
import { learningService, userService } from '@/services/api';
import { levelInfo } from '@/utils/gamification';

interface ProgressState {
    progress: Progress[];
    activity: DailyActivity[];
    unlocked: UnlockedAchievement[];
    bookmarks: Bookmark[];
    enrolledModuleIds: string[];
    /** The course currently driving the student's Daily Quiz — null until hydrated or set. */
    activeModuleId: string | null;
    xp: number;
    /** Real values from `GET /learning/dashboard` — 0 until the first successful `hydrate()`. */
    streakDays: number;
    longestStreak: number;
    lessonsCompleted: number;
    /** Per-module completion percent from the backend, keyed by module id (as string). */
    moduleCompletion: Record<string, number>;
    loading: boolean;

    hydrate: () => Promise<void>;
    completeLesson: (lessonId: string, moduleId: string, xpReward: number) => Promise<number>;
    awardXp: (amount: number) => { levelUp: boolean; level: number };
    recordMinutes: (minutes: number) => void;

    toggleBookmark: (payload: Omit<Bookmark, 'id' | 'createdAt'>) => void;
    isBookmarked: (kind: Bookmark['kind'], refId: string) => boolean;

    toggleEnrollment: (moduleId: string) => void;
    isEnrolled: (moduleId: string) => boolean;
    /** Syncs local state after a real server-side enroll (checkout/payment) — no API call, unlike `toggleEnrollment`. */
    markEnrolled: (moduleId: string) => void;

    setActiveModule: (moduleSlug: string, moduleId: string) => void;
    isActiveModule: (moduleId: string) => boolean;

    lessonStatus: (lessonId: string) => Progress['status'];
    modulePercent: (moduleId: string) => number;
    todayMinutes: () => number;
}

const today = () => new Date().toISOString().slice(0, 10);

export const useProgressStore = create<ProgressState>()(
    persist(
        (set, get) => ({
            progress: [],
            activity: [],
            unlocked: [],
            bookmarks: [],
            enrolledModuleIds: [],
            activeModuleId: null,
            xp: 0,
            streakDays: 0,
            longestStreak: 0,
            lessonsCompleted: 0,
            moduleCompletion: {},
            loading: false,

            hydrate: async () => {
                set({ loading: true });

                // Some of these endpoints don't exist yet (achievements/bookmarks
                // are still mock-only) — `allSettled` means one 404 no longer
                // freezes every other piece of progress state along with it.
                const [dashboardResult, progressResult, activityResult, unlockedResult, bookmarksResult] =
                    await Promise.allSettled([
                        learningService.getDashboard(),
                        learningService.listProgress(),
                        userService.getActivity(),
                        userService.listUnlocked(),
                        userService.listBookmarks(),
                    ]);

                set((state) => {
                    const next: Partial<ProgressState> = { loading: false };

                    if (progressResult.status === 'fulfilled') next.progress = progressResult.value;
                    if (activityResult.status === 'fulfilled') next.activity = activityResult.value;
                    if (unlockedResult.status === 'fulfilled') next.unlocked = unlockedResult.value;
                    if (bookmarksResult.status === 'fulfilled') next.bookmarks = bookmarksResult.value;

                    const dashboard = dashboardResult.status === 'fulfilled' ? dashboardResult.value : null;

                    if (dashboard) {
                        next.xp = dashboard.xp_total;
                        next.streakDays = dashboard.stat.streak_days;
                        next.longestStreak = dashboard.stat.longest_streak;
                        next.lessonsCompleted = dashboard.stat.lessons_completed;
                        next.moduleCompletion = Object.fromEntries(
                            dashboard.modules.map((module) => [String(module.id), module.completion_percent]),
                        );

                        // Merge real per-day XP into whatever local activity rows
                        // already exist (from `recordMinutes()`) rather than
                        // replacing them outright — the backend only tracks
                        // cumulative minutes, not a per-day breakdown, so that
                        // part of each row has to stay locally sourced.
                        const base = next.activity ?? state.activity;
                        const byDate = new Map(base.map((row) => [row.date, row]));

                        for (const [date, xp] of Object.entries(dashboard.xp_per_day)) {
                            const existing = byDate.get(date);
                            byDate.set(
                                date,
                                existing ? { ...existing, xp } : { date, minutes: 0, xp, lessonsCompleted: 0 },
                            );
                        }

                        next.activity = [...byDate.values()].sort((a, b) => a.date.localeCompare(b.date));
                        next.activeModuleId = dashboard.active_module_id;
                    }

                    return next;
                });
            },

            completeLesson: async (lessonId, moduleId, xpReward) => {
                const { xpEarned } = await learningService.completeLesson(lessonId, xpReward);

                set((state) => {
                    const existing = state.progress.find((p) => p.lessonId === lessonId);

                    const progress = existing
                        ? state.progress.map((p) =>
                              p.lessonId === lessonId
                                  ? { ...p, status: 'completed' as const, percent: 100, completedAt: new Date().toISOString() }
                                  : p,
                          )
                        : [
                              ...state.progress,
                              {
                                  id: `p-${Date.now()}`,
                                  userId: 'local',
                                  moduleId,
                                  lessonId,
                                  status: 'completed' as const,
                                  percent: 100,
                                  score: null,
                                  completedAt: new Date().toISOString(),
                              },
                          ];

                    return { progress };
                });

                get().awardXp(xpEarned);

                return xpEarned;
            },

            awardXp: (amount) => {
                const before = levelInfo(get().xp).level;
                const xp = get().xp + amount;

                // Mirror the XP onto today's activity row so the chart and the
                // daily goal ring stay in step with the counter.
                set((state) => {
                    const date = today();
                    const rows = [...state.activity];
                    const index = rows.findIndex((row) => row.date === date);

                    if (index >= 0) {
                        rows[index] = { ...rows[index], xp: rows[index].xp + amount };
                    } else {
                        rows.push({ date, minutes: 0, xp: amount, lessonsCompleted: 0 });
                    }

                    return { xp, activity: rows };
                });

                const after = levelInfo(xp).level;

                return { levelUp: after > before, level: after };
            },

            recordMinutes: (minutes) => {
                set((state) => {
                    const date = today();
                    const rows = [...state.activity];
                    const index = rows.findIndex((row) => row.date === date);

                    if (index >= 0) {
                        rows[index] = { ...rows[index], minutes: rows[index].minutes + minutes };
                    } else {
                        rows.push({ date, minutes, xp: 0, lessonsCompleted: 0 });
                    }

                    return { activity: rows };
                });
            },

            toggleBookmark: (payload) => {
                set((state) => {
                    const existing = state.bookmarks.find(
                        (b) => b.kind === payload.kind && b.refId === payload.refId,
                    );

                    if (existing) {
                        return { bookmarks: state.bookmarks.filter((b) => b.id !== existing.id) };
                    }

                    return {
                        bookmarks: [
                            { ...payload, id: `bm-${Date.now()}`, createdAt: new Date().toISOString() },
                            ...state.bookmarks,
                        ],
                    };
                });

                // Fire-and-forget sync; the local state is the source of truth
                // for the current session either way.
                void userService.toggleBookmark(payload).catch(() => undefined);
            },

            isBookmarked: (kind, refId) =>
                get().bookmarks.some((b) => b.kind === kind && b.refId === refId),

            toggleEnrollment: (moduleId) => {
                set((state) => ({
                    enrolledModuleIds: state.enrolledModuleIds.includes(moduleId)
                        ? state.enrolledModuleIds.filter((id) => id !== moduleId)
                        : [...state.enrolledModuleIds, moduleId],
                }));

                // Fire-and-forget sync; the local state is the source of truth
                // for the current session either way.
                void learningService.toggleEnrollment(moduleId).catch(() => undefined);
            },

            isEnrolled: (moduleId) => get().enrolledModuleIds.includes(moduleId),

            markEnrolled: (moduleId) => {
                set((state) =>
                    state.enrolledModuleIds.includes(moduleId)
                        ? state
                        : { enrolledModuleIds: [...state.enrolledModuleIds, moduleId] },
                );
            },

            setActiveModule: (moduleSlug, moduleId) => {
                set({ activeModuleId: moduleId });

                // Fire-and-forget sync, same pattern as toggleEnrollment above.
                void learningService.setActiveModule(moduleSlug).catch(() => undefined);
            },

            isActiveModule: (moduleId) => get().activeModuleId === moduleId,

            lessonStatus: (lessonId) =>
                get().progress.find((p) => p.lessonId === lessonId)?.status ?? 'not-started',

            modulePercent: (moduleId) => {
                const real = get().moduleCompletion[moduleId];

                if (real !== undefined) return real;

                const rows = get().progress.filter((p) => p.moduleId === moduleId && p.lessonId);

                if (rows.length === 0) {
                    return get().progress.find((p) => p.moduleId === moduleId && !p.lessonId)?.percent ?? 0;
                }

                const done = rows.filter((p) => p.status === 'completed').length;

                return Math.round((done / rows.length) * 100);
            },

            todayMinutes: () => get().activity.find((row) => row.date === today())?.minutes ?? 0,
        }),
        {
            name: 'ea.progress',
            partialize: (state) => ({
                progress: state.progress,
                activity: state.activity,
                unlocked: state.unlocked,
                bookmarks: state.bookmarks,
                enrolledModuleIds: state.enrolledModuleIds,
                activeModuleId: state.activeModuleId,
                xp: state.xp,
                streakDays: state.streakDays,
                longestStreak: state.longestStreak,
                lessonsCompleted: state.lessonsCompleted,
                moduleCompletion: state.moduleCompletion,
            }),
        },
    ),
);

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
    xp: number;
    loading: boolean;

    hydrate: () => Promise<void>;
    completeLesson: (lessonId: string, moduleId: string) => Promise<number>;
    awardXp: (amount: number) => { levelUp: boolean; level: number };
    recordMinutes: (minutes: number) => void;

    toggleBookmark: (payload: Omit<Bookmark, 'id' | 'createdAt'>) => void;
    isBookmarked: (kind: Bookmark['kind'], refId: string) => boolean;

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
            xp: 0,
            loading: false,

            hydrate: async () => {
                set({ loading: true });

                const [progress, activity, unlocked, bookmarks] = await Promise.all([
                    learningService.listProgress(),
                    userService.getActivity(),
                    userService.listUnlocked(),
                    userService.listBookmarks(),
                ]);

                set({ progress, activity, unlocked, bookmarks, loading: false });
            },

            completeLesson: async (lessonId, moduleId) => {
                const { xpEarned } = await learningService.completeLesson(lessonId);

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

            lessonStatus: (lessonId) =>
                get().progress.find((p) => p.lessonId === lessonId)?.status ?? 'not-started',

            modulePercent: (moduleId) => {
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
                xp: state.xp,
            }),
        },
    ),
);

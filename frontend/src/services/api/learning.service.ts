import type { DashboardStats, LearningModule, Lesson, Progress } from '@/types';
import modulesData from '@/data/modules.json';
import userData from '@/data/user.json';
import { http, unwrap } from './client';
import { delay, source } from './config';

const modules = modulesData.modules as unknown as LearningModule[];
const lessons = modulesData.lessons as unknown as (Lesson & { moduleId: number })[];

/**
 * Learning (modules, lessons, enrollment) is the one domain with a real,
 * verified backend today — see app/Http/Controllers/Api/LearningController.php.
 * Its methods below call the API directly, bypassing the mock/api toggle that
 * every other service in this folder still uses, so this domain works
 * regardless of `VITE_DATA_SOURCE` while the rest of the app stays on mock
 * until their backend counterparts exist.
 */
const LIVE = import.meta.env.VITE_DATA_SOURCE === 'api';

/** `GET /learning/modules` groups modules by language name; flatten to one list. */
async function fetchAllModules(): Promise<LearningModule[]> {
    const grouped = unwrap<Record<string, LearningModule[]>>((await http.get('/learning/modules')).data);

    return Object.values(grouped).flat();
}

async function fetchLessons(moduleSlug: string): Promise<Lesson[]> {
    return unwrap<Lesson[]>((await http.get(`/learning/modules/${moduleSlug}/lessons`)).data);
}

async function fetchLesson(id: string): Promise<Lesson> {
    return unwrap<Lesson>((await http.get(`/learning/lessons/${id}`)).data);
}

export const learningService = {
    listModules: async (filters?: { content_type?: string }): Promise<LearningModule[]> => {
        const all = LIVE
            ? await fetchAllModules()
            : await source(() => delay(modules), fetchAllModules);

        return all.filter((m) => !filters?.content_type || m.content_type === filters.content_type);
    },

    /** No single-module endpoint exists on the backend — reuse the list and find by slug. */
    getModule: async (slug: string): Promise<LearningModule | null> => {
        if (LIVE) {
            const all = await fetchAllModules();

            return all.find((m) => m.slug === slug) ?? null;
        }

        return source(() => delay(modules.find((m) => m.slug === slug) ?? null), async () => null);
    },

    listLessons: (moduleSlug: string): Promise<Lesson[]> =>
        LIVE
            ? fetchLessons(moduleSlug)
            : source(
                  () => {
                      const mod = modules.find((m) => m.slug === moduleSlug);

                      return delay(mod ? lessons.filter((l) => l.moduleId === mod.id) : []);
                  },
                  () => fetchLessons(moduleSlug),
              ),

    /** `id` here is a lesson slug — the backend binds `Lesson` routes by slug, not numeric id. */
    getLesson: (slug: string): Promise<Lesson | null> =>
        LIVE
            ? fetchLesson(slug)
            : source(
                  () => delay(lessons.find((l) => l.slug === slug) ?? null),
                  () => fetchLesson(slug),
              ),

    /** Previous / next lesson inside the same module, for the lesson player. */
    getNeighbours: async (lessonSlug: string) => {
        if (LIVE) {
            const current = await fetchLesson(lessonSlug);
            const moduleSlug = current.module?.slug;

            if (!moduleSlug) return { previous: null, next: null };

            const siblings = await fetchLessons(moduleSlug);
            const index = siblings.findIndex((l) => l.slug === lessonSlug);

            return {
                previous: index > 0 ? siblings[index - 1] : null,
                next: index >= 0 && index < siblings.length - 1 ? siblings[index + 1] : null,
            };
        }

        const current = lessons.find((l) => l.slug === lessonSlug);

        if (!current) return { previous: null, next: null };

        const siblings = lessons.filter((l) => l.moduleId === current.moduleId);
        const index = siblings.findIndex((l) => l.slug === lessonSlug);

        return {
            previous: index > 0 ? siblings[index - 1] : null,
            next: index < siblings.length - 1 ? siblings[index + 1] : null,
        };
    },

    listProgress: () =>
        source(
            () => delay(userData.progress as Progress[]),
            async () => unwrap<Progress[]>((await http.get('/learning/progress')).data),
        ),

    /**
     * XP, level, streak, 30-day XP series, per-module completion, and recent
     * XP log entries in one call — see ProgressService::dashboardFor(). No
     * mock equivalent exists (the mock dashboard is computed entirely from
     * local Zustand state), so this is `null` outside `api` mode; the store
     * simply skips merging it in and keeps its local-only numbers.
     */
    getDashboard: (): Promise<DashboardStats | null> =>
        LIVE
            ? http.get('/learning/dashboard').then((res) => unwrap<DashboardStats>(res.data))
            : Promise.resolve(null),

    /** `xpReward` is the lesson's own reward — the backend awards exactly this amount. */
    completeLesson: async (lessonId: string, xpReward: number): Promise<{ lessonId: string; xpEarned: number }> => {
        if (LIVE) {
            await http.post(`/learning/lessons/${lessonId}/complete`);

            return { lessonId, xpEarned: xpReward };
        }

        return source(
            () => delay({ lessonId, xpEarned: xpReward }),
            async () => {
                await http.post(`/learning/lessons/${lessonId}/complete`);

                return { lessonId, xpEarned: xpReward };
            },
        );
    },

    /** Fire-and-forget sync; the store decides the new local state before calling this. */
    toggleEnrollment: (moduleSlug: string): Promise<{ module: string; enrolled: boolean }> =>
        LIVE
            ? http
                  .post(`/learning/modules/${moduleSlug}/enroll`)
                  .then((res) => unwrap<{ module: string; enrolled: boolean }>(res.data))
            : source(
                  () => delay({ module: moduleSlug, enrolled: true }),
                  async () =>
                      unwrap<{ module: string; enrolled: boolean }>(
                          (await http.post(`/learning/modules/${moduleSlug}/enroll`)).data,
                      ),
              ),

    /** Fire-and-forget sync; the store decides the new local state before calling this. */
    setActiveModule: (moduleSlug: string): Promise<{ module: string; activeModuleId: string }> =>
        LIVE
            ? http
                  .post(`/learning/modules/${moduleSlug}/set-active`)
                  .then((res) => unwrap<{ module: string; activeModuleId: string }>(res.data))
            : source(
                  () => delay({ module: moduleSlug, activeModuleId: moduleSlug }),
                  async () =>
                      unwrap<{ module: string; activeModuleId: string }>(
                          (await http.post(`/learning/modules/${moduleSlug}/set-active`)).data,
                      ),
              ),
};

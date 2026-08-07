import type { LearningModule, Lesson, Progress, SkillType } from '@/types';
import modulesData from '@/data/modules.json';
import userData from '@/data/user.json';
import { http, unwrap } from './client';
import { delay, source } from './config';

const modules = modulesData.modules as LearningModule[];
const lessons = modulesData.lessons as Lesson[];

export const learningService = {
    listModules: (filters?: { category?: SkillType; level?: string }) =>
        source(
            () =>
                delay(
                    modules
                        .filter((m) => !filters?.category || m.category === filters.category)
                        .filter((m) => !filters?.level || m.level === filters.level)
                        .sort((a, b) => a.order - b.order),
                ),
            async () => unwrap<LearningModule[]>((await http.get('/learning/modules', { params: filters })).data),
        ),

    getModule: (id: string) =>
        source(
            () => delay(modules.find((m) => m.id === id) ?? null),
            async () => unwrap<LearningModule>((await http.get(`/learning/modules/${id}`)).data),
        ),

    listLessons: (moduleId: string) =>
        source(
            () => delay(lessons.filter((l) => l.moduleId === moduleId).sort((a, b) => a.order - b.order)),
            async () => unwrap<Lesson[]>((await http.get(`/learning/modules/${moduleId}/lessons`)).data),
        ),

    getLesson: (id: string) =>
        source(
            () => delay(lessons.find((l) => l.id === id) ?? null),
            async () => unwrap<Lesson>((await http.get(`/learning/lessons/${id}`)).data),
        ),

    /** Previous / next lesson inside the same module, for the lesson player. */
    getNeighbours: async (lessonId: string) => {
        const current = lessons.find((l) => l.id === lessonId);

        if (!current) return { previous: null, next: null };

        const siblings = lessons
            .filter((l) => l.moduleId === current.moduleId)
            .sort((a, b) => a.order - b.order);

        const index = siblings.findIndex((l) => l.id === lessonId);

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

    completeLesson: (lessonId: string) =>
        source(
            () => delay({ lessonId, xpEarned: lessons.find((l) => l.id === lessonId)?.xpReward ?? 20 }),
            async () => unwrap<{ lessonId: string; xpEarned: number }>(
                (await http.post(`/learning/lessons/${lessonId}/complete`)).data,
            ),
        ),
};

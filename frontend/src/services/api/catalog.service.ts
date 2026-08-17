import type { LearningModule, Lesson } from '@/types';
import { http, unwrap } from './client';

/**
 * Public, unauthenticated course catalog for the landing page — like
 * `dailyQuizService`/`vocabularyBankService`, this always hits the real API
 * (there's no mock counterpart), but unlike those, these routes work without
 * a bearer token at all (`GET /public/modules[...]`).
 */
export const catalogService = {
    listModules: (filters?: { language?: string; content_type?: string; level?: string }): Promise<LearningModule[]> =>
        http.get('/public/modules', { params: filters }).then((res) => unwrap<LearningModule[]>(res.data)),

    getModule: (moduleSlug: string): Promise<{ module: LearningModule; lessons: Lesson[] } | null> =>
        http
            .get(`/public/modules/${moduleSlug}`)
            .then((res) => unwrap<{ module: LearningModule; lessons: Lesson[] }>(res.data))
            .catch(() => null),
};

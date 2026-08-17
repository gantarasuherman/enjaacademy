import type {
    DailyQuizAttempt,
    DailyQuizResult,
    DailyQuizStatus,
    VocabularyBankWord,
    VocabularyLevelProgress,
    WeakWord,
} from '@/types';
import { http, unwrap } from './client';

/**
 * The Daily Quiz / vocabulary-bank feature is brand new with no mock
 * counterpart yet — like `learningService` and `jlptGrammarService`, it
 * always hits the real API regardless of `VITE_DATA_SOURCE`.
 */
export const dailyQuizService = {
    status: async (): Promise<DailyQuizStatus> => unwrap<DailyQuizStatus>((await http.get('/daily-quiz/status')).data),

    getToday: async (): Promise<DailyQuizAttempt> => unwrap<DailyQuizAttempt>((await http.get('/daily-quiz')).data),

    submit: async (
        attemptId: string,
        answers: { questionId: string; answer: string }[],
    ): Promise<DailyQuizResult> =>
        unwrap<DailyQuizResult>(
            (
                await http.post('/daily-quiz/submit', {
                    attemptId: Number(attemptId),
                    answers: answers.map((a) => ({ questionId: Number(a.questionId), answer: a.answer })),
                })
            ).data,
        ),

    skip: async (): Promise<DailyQuizAttempt> => unwrap<DailyQuizAttempt>((await http.post('/daily-quiz/skip')).data),
};

export const weakWordService = {
    list: async (): Promise<WeakWord[]> => unwrap<WeakWord[]>((await http.get('/weak-words')).data),
};

export interface VocabularyBankPage {
    words: VocabularyBankWord[];
    page: number;
    lastPage: number;
    total: number;
}

export const vocabularyBankService = {
    /** Server-paginated — the bank is meant to grow to 20,000+ words, so this never fetches the whole set. */
    list: async (params?: {
        language?: 'english' | 'japanese';
        level?: string;
        search?: string;
        page?: number;
        perPage?: number;
    }): Promise<VocabularyBankPage> => {
        const res = await http.get('/vocabulary-words', { params });
        const meta = res.data.meta as { current_page: number; last_page: number; total: number };

        return {
            words: res.data.data as VocabularyBankWord[],
            page: meta.current_page,
            lastPage: meta.last_page,
            total: meta.total,
        };
    },

    progress: async (): Promise<VocabularyLevelProgress[]> =>
        unwrap<VocabularyLevelProgress[]>((await http.get('/vocabulary-words/progress')).data),
};

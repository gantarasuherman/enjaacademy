import type { AnswerRecord, Question, Quiz, QuizResult } from '@/types';
import quizData from '@/data/quizzes.json';
import { http, unwrap } from './client';
import { delay, source } from './config';

const quizzes = quizData.quizzes as Quiz[];
const questions = quizData.questions as Question[];

/** Loose comparison for typed answers: case, punctuation and spacing are noise. */
function normalise(value: string): string {
    return value
        .toLowerCase()
        .replace(/[.,!?;:'"]/g, '')
        .replace(/\s+/g, ' ')
        .trim();
}

/**
 * Grading lives in the service, not the component, so the same rules apply
 * whether a quiz is graded locally (mock) or by Laravel (api).
 */
export function gradeAnswer(question: Question, given: string | string[]): boolean {
    const key = question.correctAnswer;

    if (Array.isArray(key)) {
        if (!Array.isArray(given) || given.length !== key.length) return false;

        return key.every((expected, i) => normalise(given[i] ?? '') === normalise(expected));
    }

    if (Array.isArray(given)) return false;

    switch (question.type) {
        case 'typing':
        case 'fill-blank':
        case 'speaking':
            // "I didn't see him" should pass against "I did not see him".
            return normalise(expandContractions(given)) === normalise(expandContractions(key));
        default:
            return given === key;
    }
}

function expandContractions(value: string): string {
    return value
        .replace(/n't\b/g, ' not')
        .replace(/'re\b/g, ' are')
        .replace(/'ve\b/g, ' have')
        .replace(/'ll\b/g, ' will')
        .replace(/'m\b/g, ' am');
}

export const quizService = {
    list: (filters?: { moduleId?: string; cefr?: string }) =>
        source(
            () =>
                delay(
                    quizzes
                        .filter((q) => !filters?.moduleId || q.moduleId === filters.moduleId)
                        .filter((q) => !filters?.cefr || q.cefr === filters.cefr),
                ),
            async () => unwrap<Quiz[]>((await http.get('/quizzes', { params: filters })).data),
        ),

    get: (id: string) =>
        source(
            () => delay(quizzes.find((q) => q.id === id) ?? null),
            async () => unwrap<Quiz>((await http.get(`/quizzes/${id}`)).data),
        ),

    /**
     * Questions for an attempt. In mock mode the answer key ships with the
     * payload (there is no server to hide it from); the API mode relies on
     * Laravel to strip it and grade server-side.
     */
    getQuestions: (quizId: string, shuffle = true) =>
        source(
            () => {
                const quiz = quizzes.find((q) => q.id === quizId);
                const list = (quiz?.questionIds ?? [])
                    .map((id) => questions.find((q) => q.id === id))
                    .filter(Boolean) as Question[];

                return delay(shuffle ? [...list].sort(() => Math.random() - 0.5) : list);
            },
            async () => unwrap<Question[]>((await http.get(`/quizzes/${quizId}/questions`)).data),
        ),

    submit: (quizId: string, answers: AnswerRecord[], durationSeconds: number) =>
        source(
            () => {
                const quiz = quizzes.find((q) => q.id === quizId);
                const correctCount = answers.filter((a) => a.correct).length;
                const totalCount = answers.length || 1;
                const score = Math.round((correctCount / totalCount) * 100);
                const passed = score >= (quiz?.passScore ?? 70);

                const result: QuizResult = {
                    id: `res-${Date.now()}`,
                    quizId,
                    score,
                    correctCount,
                    totalCount: answers.length,
                    passed,
                    earnedXp: passed
                        ? (quiz?.xpReward ?? 50) + (correctCount === answers.length ? 25 : 0)
                        : 0,
                    durationSeconds,
                    answers,
                    completedAt: new Date().toISOString(),
                };

                return delay(result, 400);
            },
            async () =>
                unwrap<QuizResult>(
                    (await http.post(`/quizzes/${quizId}/submit`, { answers, durationSeconds })).data,
                ),
        ),

    history: () =>
        source(
            () => delay([] as QuizResult[]),
            async () => unwrap<QuizResult[]>((await http.get('/quizzes/history')).data),
        ),
};

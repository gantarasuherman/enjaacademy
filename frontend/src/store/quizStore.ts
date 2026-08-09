import { create } from 'zustand';
import type { AnswerRecord, Question, Quiz, QuizAttemptsOverview, QuizResult } from '@/types';
import { gradeAnswer, quizService } from '@/services/api';

type Phase = 'idle' | 'loading' | 'active' | 'submitting' | 'finished';

interface QuizState {
    quiz: Quiz | null;
    questions: Question[];
    index: number;
    answers: Record<string, string | string[]>;
    records: AnswerRecord[];
    result: QuizResult | null;
    phase: Phase;
    secondsLeft: number | null;
    startedAt: number | null;
    questionStartedAt: number | null;
    error: string | null;
    /** Attempt history/stats/limit for the quiz currently being viewed — read by the pre-quiz info screen and the result screen. */
    overview: QuizAttemptsOverview | null;
    overviewLoading: boolean;

    loadOverview: (quizId: string) => Promise<void>;
    start: (quizId: string) => Promise<void>;
    setAnswer: (questionId: string, value: string | string[]) => void;
    next: () => void;
    previous: () => void;
    goTo: (index: number) => void;
    tick: () => void;
    submit: () => Promise<QuizResult | null>;
    reset: () => void;

    current: () => Question | null;
    progressPercent: () => number;
    answeredCount: () => number;
}

export const useQuizStore = create<QuizState>((set, get) => ({
    quiz: null,
    questions: [],
    index: 0,
    answers: {},
    records: [],
    result: null,
    phase: 'idle',
    secondsLeft: null,
    startedAt: null,
    questionStartedAt: null,
    error: null,
    overview: null,
    overviewLoading: false,

    loadOverview: async (quizId) => {
        set({ overviewLoading: true });

        try {
            const overview = await quizService.getAttempts(quizId);
            set({ overview, overviewLoading: false });
        } catch {
            set({ overviewLoading: false });
        }
    },

    start: async (quizId) => {
        set({ phase: 'loading', error: null, result: null, answers: {}, records: [], index: 0 });

        try {
            const [quiz, questions] = await Promise.all([
                quizService.get(quizId),
                quizService.getQuestions(quizId),
            ]);

            if (!quiz || questions.length === 0) {
                set({ phase: 'idle', error: 'Kuis tidak ditemukan atau belum punya soal.' });
                return;
            }

            set({
                quiz,
                questions,
                phase: 'active',
                secondsLeft: quiz.timeLimitSeconds,
                startedAt: Date.now(),
                questionStartedAt: Date.now(),
            });
        } catch (error) {
            set({
                phase: 'idle',
                error: error instanceof Error ? error.message : 'Gagal memuat kuis.',
            });
        }
    },

    setAnswer: (questionId, value) => set((state) => ({ answers: { ...state.answers, [questionId]: value } })),

    next: () => {
        const { index, questions, questionStartedAt, answers } = get();
        const question = questions[index];

        // Record the answer as we leave the question so per-question timing is
        // accurate even when the learner navigates back and forth.
        if (question) {
            const given = answers[question.id] ?? '';
            const record: AnswerRecord = {
                questionId: question.id,
                given,
                correct: gradeAnswer(question, given),
                timeSpentSeconds: Math.round((Date.now() - (questionStartedAt ?? Date.now())) / 1000),
            };

            set((state) => ({
                records: [...state.records.filter((r) => r.questionId !== question.id), record],
            }));
        }

        if (index < questions.length - 1) {
            set({ index: index + 1, questionStartedAt: Date.now() });
        }
    },

    previous: () => {
        const { index } = get();
        if (index > 0) set({ index: index - 1, questionStartedAt: Date.now() });
    },

    goTo: (index) => {
        const { questions } = get();
        if (index >= 0 && index < questions.length) set({ index, questionStartedAt: Date.now() });
    },

    tick: () => {
        const { secondsLeft, phase } = get();

        if (phase !== 'active' || secondsLeft === null) return;

        if (secondsLeft <= 1) {
            set({ secondsLeft: 0 });
            void get().submit();
            return;
        }

        set({ secondsLeft: secondsLeft - 1 });
    },

    submit: async () => {
        const { quiz, questions, answers, startedAt, phase } = get();

        if (!quiz || phase === 'submitting' || phase === 'finished') return get().result;

        set({ phase: 'submitting' });

        // Grade every question, including ones never visited (counted wrong).
        const records: AnswerRecord[] = questions.map((question) => {
            const existing = get().records.find((r) => r.questionId === question.id);

            if (existing) return existing;

            const given = answers[question.id] ?? '';

            return {
                questionId: question.id,
                given,
                correct: gradeAnswer(question, given),
                timeSpentSeconds: 0,
            };
        });

        const duration = Math.round((Date.now() - (startedAt ?? Date.now())) / 1000);

        try {
            const result = await quizService.submit(quiz.id, records, duration);
            set({ result, records, phase: 'finished' });
            void get().loadOverview(quiz.id);
            return result;
        } catch (error) {
            set({
                phase: 'active',
                error: error instanceof Error ? error.message : 'Gagal mengirim jawaban.',
            });
            return null;
        }
    },

    reset: () =>
        set({
            quiz: null,
            questions: [],
            index: 0,
            answers: {},
            records: [],
            result: null,
            phase: 'idle',
            secondsLeft: null,
            startedAt: null,
            questionStartedAt: null,
            error: null,
        }),
    // `overview` deliberately survives reset() — QuizResultPage reads it right
    // after QuizPlayPage unmounts and calls reset() on its way to /result.

    current: () => get().questions[get().index] ?? null,

    progressPercent: () => {
        const { index, questions } = get();
        return questions.length === 0 ? 0 : Math.round(((index + 1) / questions.length) * 100);
    },

    answeredCount: () => {
        const { answers } = get();
        return Object.values(answers).filter((value) =>
            Array.isArray(value) ? value.length > 0 : String(value).trim() !== '',
        ).length;
    },
}));

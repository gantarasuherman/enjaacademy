import type { FlashcardDeck, FlashcardItem, ReviewGrade, ReviewState } from '@/types';
import flashcardData from '@/data/flashcards.json';
import { http, unwrap } from './client';
import { delay, source } from './config';

const decks = flashcardData.decks as FlashcardDeck[];
const cards = flashcardData.cards as FlashcardItem[];

/** SM-2 quality values behind the four review buttons. */
const GRADE_QUALITY: Record<ReviewGrade, number> = {
    again: 1,
    hard: 3,
    good: 4,
    easy: 5,
};

/**
 * One SM-2 step. Returns the next review state; the caller persists it.
 */
export function scheduleNext(current: ReviewState | undefined, grade: ReviewGrade): ReviewState {
    const quality = GRADE_QUALITY[grade];

    let ease = current?.ease ?? 2.5;
    let repetitions = current?.repetitions ?? 0;
    let intervalDays = current?.intervalDays ?? 0;
    let lapses = current?.lapses ?? 0;

    if (quality < 3) {
        // A lapse restarts the ladder but keeps the (reduced) ease factor.
        repetitions = 0;
        intervalDays = 1;
        lapses += 1;
    } else {
        repetitions += 1;
        intervalDays =
            repetitions === 1 ? 1 : repetitions === 2 ? 6 : Math.max(1, Math.round(intervalDays * ease));
    }

    // Canonical SM-2 ease adjustment, clamped at the 1.3 floor.
    ease = Math.max(1.3, ease + (0.1 - (5 - quality) * (0.08 + (5 - quality) * 0.02)));

    const dueAt = new Date();
    dueAt.setDate(dueAt.getDate() + intervalDays);

    return {
        cardId: current?.cardId ?? '',
        ease: Number(ease.toFixed(2)),
        intervalDays,
        repetitions,
        lapses,
        dueAt: dueAt.toISOString(),
        lastReviewedAt: new Date().toISOString(),
    };
}

export const flashcardService = {
    listDecks: () =>
        source(
            () => delay(decks),
            async () => unwrap<FlashcardDeck[]>((await http.get('/flashcards/decks')).data),
        ),

    getDeck: (id: string) =>
        source(
            () => delay(decks.find((d) => d.id === id) ?? null),
            async () => unwrap<FlashcardDeck>((await http.get(`/flashcards/decks/${id}`)).data),
        ),

    listCards: (deckId: string) =>
        source(
            () => delay(cards.filter((c) => c.deckId === deckId)),
            async () => unwrap<FlashcardItem[]>((await http.get(`/flashcards/decks/${deckId}/cards`)).data),
        ),

    /**
     * Cards whose review is due. Unseen cards come first, then the most
     * overdue — the ordering that keeps a session feeling productive.
     */
    listDue: (deckId: string | null, reviews: Record<string, ReviewState>, limit = 20) => {
        const pool = deckId ? cards.filter((c) => c.deckId === deckId) : cards;
        const now = Date.now();

        const due = pool.filter((card) => {
            const review = reviews[card.id];
            return !review || new Date(review.dueAt).getTime() <= now;
        });

        due.sort((a, b) => {
            const ra = reviews[a.id];
            const rb = reviews[b.id];

            if (!ra && !rb) return 0;
            if (!ra) return -1;
            if (!rb) return 1;

            return new Date(ra.dueAt).getTime() - new Date(rb.dueAt).getTime();
        });

        return due.slice(0, limit);
    },

    dueCount: (reviews: Record<string, ReviewState>) => {
        const now = Date.now();

        return cards.filter((card) => {
            const review = reviews[card.id];
            return !review || new Date(review.dueAt).getTime() <= now;
        }).length;
    },

    syncReview: (cardId: string, state: ReviewState) =>
        source(
            () => delay(state, 0),
            async () => unwrap<ReviewState>((await http.post(`/flashcards/${cardId}/review`, state)).data),
        ),
};

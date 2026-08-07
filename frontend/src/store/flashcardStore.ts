import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import type { FlashcardItem, ReviewGrade, ReviewState } from '@/types';
import { flashcardService, scheduleNext } from '@/services/api';

interface FlashcardState {
    /** SM-2 state keyed by card id. */
    reviews: Record<string, ReviewState>;
    queue: FlashcardItem[];
    index: number;
    flipped: boolean;
    sessionStats: { reviewed: number; again: number; good: number };

    startSession: (deckId: string | null, limit?: number) => Promise<void>;
    flip: () => void;
    grade: (grade: ReviewGrade) => void;
    endSession: () => void;

    dueCount: () => number;
    currentCard: () => FlashcardItem | null;
    remaining: () => number;
}

export const useFlashcardStore = create<FlashcardState>()(
    persist(
        (set, get) => ({
            reviews: {},
            queue: [],
            index: 0,
            flipped: false,
            sessionStats: { reviewed: 0, again: 0, good: 0 },

            startSession: async (deckId, limit = 20) => {
                // Ensure the deck's cards are loaded before selecting due ones.
                if (deckId) await flashcardService.listCards(deckId);

                const queue = flashcardService.listDue(deckId, get().reviews, limit);

                set({
                    queue,
                    index: 0,
                    flipped: false,
                    sessionStats: { reviewed: 0, again: 0, good: 0 },
                });
            },

            flip: () => set((state) => ({ flipped: !state.flipped })),

            grade: (grade) => {
                const card = get().currentCard();
                if (!card) return;

                const next = scheduleNext({ ...get().reviews[card.id], cardId: card.id }, grade);

                set((state) => ({
                    reviews: { ...state.reviews, [card.id]: { ...next, cardId: card.id } },
                    index: state.index + 1,
                    flipped: false,
                    sessionStats: {
                        reviewed: state.sessionStats.reviewed + 1,
                        again: state.sessionStats.again + (grade === 'again' ? 1 : 0),
                        good: state.sessionStats.good + (grade === 'again' ? 0 : 1),
                    },
                }));

                // A card graded "again" comes back at the end of this session
                // rather than waiting a full day.
                if (grade === 'again') {
                    set((state) => ({ queue: [...state.queue, card] }));
                }

                void flashcardService.syncReview(card.id, next).catch(() => undefined);
            },

            endSession: () => set({ queue: [], index: 0, flipped: false }),

            dueCount: () => flashcardService.dueCount(get().reviews),

            currentCard: () => get().queue[get().index] ?? null,

            remaining: () => Math.max(0, get().queue.length - get().index),
        }),
        {
            name: 'ea.flashcards',
            partialize: (state) => ({ reviews: state.reviews }),
        },
    ),
);

<?php

declare(strict_types=1);

namespace App\Services\Learning;

use App\DataTransferObjects\FlashcardDeckData;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\FlashcardReview;
use App\Models\User;
use App\Repositories\Contracts\FlashcardDeckRepositoryInterface;
use App\Repositories\Contracts\FlashcardRepositoryInterface;
use App\Services\Gamification\ProgressService;
use Illuminate\Support\Facades\DB;

/**
 * Spaced repetition built on SM-2.
 */
class FlashcardService
{
    public function __construct(
        private readonly FlashcardDeckRepositoryInterface $decks,
        private readonly FlashcardRepositoryInterface $cards,
        private readonly ProgressService $progress,
    ) {}

    public function createDeck(FlashcardDeckData $data): FlashcardDeck
    {
        return DB::transaction(function () use ($data) {
            /** @var FlashcardDeck $deck */
            $deck = $this->decks->create($data->toArray());

            if ($data->lessonItemIds !== []) {
                $this->cards->createFromLessonItems($deck->id, $data->lessonItemIds);
            }

            return $deck->refresh();
        });
    }

    public function updateDeck(FlashcardDeck $deck, FlashcardDeckData $data): FlashcardDeck
    {
        $this->decks->update($deck, $data->toArray());

        if ($data->lessonItemIds !== []) {
            $this->cards->createFromLessonItems($deck->id, $data->lessonItemIds);
        }

        return $deck->refresh();
    }

    public function deleteDeck(FlashcardDeck $deck): void
    {
        $this->decks->delete($deck);
    }

    /**
     * Apply one SM-2 grading. `$quality` is 0..5 — in the UI: Again=1,
     * Hard=3, Good=4, Easy=5.
     */
    public function review(User $user, Flashcard $card, int $quality): FlashcardReview
    {
        $quality = max(0, min(5, $quality));

        /** @var FlashcardReview $review */
        $review = FlashcardReview::firstOrNew([
            'user_id' => $user->id,
            'flashcard_id' => $card->id,
        ]);

        $ease = (float) ($review->ease_factor ?: 2.5);
        $repetitions = (int) $review->repetitions;
        $interval = (int) $review->interval_days;

        if ($quality < 3) {
            // Lapse: restart the ladder but keep the (reduced) ease factor.
            $repetitions = 0;
            $interval = 1;
            $review->lapses = (int) $review->lapses + 1;
        } else {
            $repetitions++;

            $interval = match ($repetitions) {
                1 => 1,
                2 => 6,
                default => (int) max(1, round($interval * $ease)),
            };
        }

        // SM-2 ease adjustment, clamped to the canonical 1.3 floor.
        $ease = max(1.3, $ease + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02)));

        $review->fill([
            'ease_factor' => round($ease, 2),
            'interval_days' => $interval,
            'repetitions' => $repetitions,
            'last_quality' => $quality,
            'last_reviewed_at' => now(),
            'due_at' => now()->addDays($interval),
        ])->save();

        $this->progress->recordFlashcardReview($user);

        return $review;
    }

    /** @return array{cards: \Illuminate\Support\Collection, due: int} */
    public function session(User $user, ?FlashcardDeck $deck = null, int $limit = 20): array
    {
        return [
            'cards' => $this->cards->dueFor($user, $deck?->id, $limit),
            'due' => $this->cards->dueCount($user),
        ];
    }
}

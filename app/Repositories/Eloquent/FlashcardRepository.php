<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Flashcard;
use App\Models\LessonItem;
use App\Models\User;
use App\Repositories\Contracts\FlashcardRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FlashcardRepository extends BaseRepository implements FlashcardRepositoryInterface
{
    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'sort_order', 'created_at'];

    public function model(): string
    {
        return Flashcard::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['deck_id'] ?? null, fn (Builder $q, $id) => $q->where('flashcard_deck_id', $id))
            ->when($filters['search'] ?? null, fn (Builder $q, $s) => $q->where(function (Builder $q) use ($s) {
                $q->where('front', 'like', "%{$s}%")->orWhere('back', 'like', "%{$s}%");
            }));
    }

    public function forDeck(int $deckId): Collection
    {
        return Flashcard::query()
            ->where('flashcard_deck_id', $deckId)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Never-reviewed cards come first, then the most overdue ones.
     */
    public function dueFor(User $user, ?int $deckId = null, int $limit = 20): Collection
    {
        return Flashcard::query()
            ->when($deckId, fn (Builder $q) => $q->where('flashcard_deck_id', $deckId))
            ->with(['deck:id,name,color'])
            ->leftJoin('flashcard_reviews', function ($join) use ($user) {
                $join->on('flashcard_reviews.flashcard_id', '=', 'flashcards.id')
                    ->where('flashcard_reviews.user_id', '=', $user->id);
            })
            ->where(function (Builder $q) {
                $q->whereNull('flashcard_reviews.id')
                    ->orWhereNull('flashcard_reviews.due_at')
                    ->orWhere('flashcard_reviews.due_at', '<=', now());
            })
            ->orderByRaw('flashcard_reviews.due_at IS NULL DESC')
            ->orderBy('flashcard_reviews.due_at')
            ->limit($limit)
            ->select('flashcards.*')
            ->get();
    }

    public function dueCount(User $user): int
    {
        return Flashcard::query()
            ->leftJoin('flashcard_reviews', function ($join) use ($user) {
                $join->on('flashcard_reviews.flashcard_id', '=', 'flashcards.id')
                    ->where('flashcard_reviews.user_id', '=', $user->id);
            })
            ->where(function (Builder $q) {
                $q->whereNull('flashcard_reviews.id')
                    ->orWhereNull('flashcard_reviews.due_at')
                    ->orWhere('flashcard_reviews.due_at', '<=', now());
            })
            ->count();
    }

    /** Turn selected lesson items into cards — the "generate deck" action. */
    public function createFromLessonItems(int $deckId, array $lessonItemIds): int
    {
        $items = LessonItem::query()->whereIn('id', $lessonItemIds)->get();

        if ($items->isEmpty()) {
            return 0;
        }

        $order = (int) Flashcard::query()->where('flashcard_deck_id', $deckId)->max('sort_order') + 1;
        $now = now();

        Flashcard::query()->insert($items->values()->map(fn (LessonItem $item, int $i) => [
            'flashcard_deck_id' => $deckId,
            'lesson_item_id' => $item->id,
            'front' => $item->term,
            'back' => collect([$item->reading, $item->romaji, $item->meaning])->filter()->implode(' — '),
            'hint' => $item->example,
            'audio_path' => $item->audio_path,
            'image_path' => $item->image_path,
            'sort_order' => $order + $i,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all());

        return $items->count();
    }
}

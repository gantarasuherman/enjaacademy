<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface FlashcardRepositoryInterface extends RepositoryInterface
{
    public function forDeck(int $deckId): Collection;

    /** Cards whose SM-2 review is due (or which have never been seen). */
    public function dueFor(User $user, ?int $deckId = null, int $limit = 20): Collection;

    public function dueCount(User $user): int;

    public function createFromLessonItems(int $deckId, array $lessonItemIds): int;
}

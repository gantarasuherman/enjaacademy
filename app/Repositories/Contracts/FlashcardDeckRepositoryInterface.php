<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\FlashcardDeck;
use App\Models\User;
use Illuminate\Support\Collection;

interface FlashcardDeckRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?FlashcardDeck;

    public function visibleFor(?User $user): Collection;

    public function forSelect(): Collection;
}

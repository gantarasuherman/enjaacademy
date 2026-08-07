<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\FlashcardDeck;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FlashcardDeckPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'flashcards';
    }

    /** Anyone may create their own personal deck. */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Model $model): bool
    {
        return $model instanceof FlashcardDeck
            && ($model->is_public || $model->isSystemDeck() || $model->user_id === $user->id || $user->can('flashcards.view'));
    }

    public function update(User $user, Model $model): bool
    {
        return $model instanceof FlashcardDeck
            && ($model->user_id === $user->id || $user->can('flashcards.update'));
    }

    public function delete(User $user, Model $model): bool
    {
        return $model instanceof FlashcardDeck
            && ($model->user_id === $user->id || $user->can('flashcards.delete'));
    }
}

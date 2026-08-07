<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FlashcardPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'flashcards';
    }

    public function update(User $user, Model $model): bool
    {
        return $model instanceof Flashcard
            && ($model->deck?->user_id === $user->id || $user->can('flashcards.update'));
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->update($user, $model);
    }
}

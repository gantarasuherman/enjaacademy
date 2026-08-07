<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class QuizPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'quizzes';
    }

    public function view(User $user, Model $model): bool
    {
        if (! $model instanceof Quiz) {
            return false;
        }

        if (! $model->is_published && ! $user->can('quizzes.update')) {
            return false;
        }

        return true;
    }

    public function attempt(User $user, Quiz $quiz): bool
    {
        return $quiz->is_published
            && $user->is_active
            && ($quiz->attemptsLeftFor($user) ?? 1) > 0;
    }
}

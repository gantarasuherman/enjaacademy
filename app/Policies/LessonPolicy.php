<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LessonPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'lessons';
    }

    public function view(User $user, Model $model): bool
    {
        if (! $model instanceof Lesson) {
            return false;
        }

        // Editors can preview drafts; students only see published lessons.
        if (! $model->is_published && ! $user->can('lessons.update')) {
            return false;
        }

        return $user->can($model->module?->permission('view') ?? 'lessons.view')
            || $user->can('lessons.view');
    }
}

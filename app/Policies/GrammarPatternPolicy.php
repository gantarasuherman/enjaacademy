<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GrammarPattern;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class GrammarPatternPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'grammar';
    }

    /** Any authenticated user may read a published pattern; editors may preview drafts. */
    public function view(User $user, Model $model): bool
    {
        if (! $model instanceof GrammarPattern) {
            return false;
        }

        return $model->is_published || $user->can('grammar.update');
    }
}

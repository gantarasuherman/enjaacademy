<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'users';
    }

    /** Everyone may open their own profile. */
    public function view(User $user, Model $model): bool
    {
        return $user->is($model) || $user->can('users.view');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->is($model) || $user->can('users.update');
    }

    /**
     * Nobody deletes themselves, and only a super admin may delete another
     * super admin.
     */
    public function delete(User $user, Model $model): bool
    {
        if ($user->is($model)) {
            return false;
        }

        if ($model instanceof User && $model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->can('users.delete');
    }

    public function assignRoles(User $user, Model $model): bool
    {
        return $user->can('users.update') && $user->can('roles.view');
    }

    public function impersonate(User $user, Model $model): bool
    {
        return $user->isSuperAdmin() && ! $user->is($model);
    }
}

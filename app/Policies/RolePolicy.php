<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RolePolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'roles';
    }

    /** The super role is read-only outside of a super admin session. */
    public function update(User $user, Model $model): bool
    {
        if ($model->name === config('admin.super_role') && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->can('roles.update');
    }

    public function delete(User $user, Model $model): bool
    {
        if ($model->name === config('admin.super_role')) {
            return false;
        }

        return $user->can('roles.delete');
    }

    public function managePermissions(User $user): bool
    {
        return $user->can('roles.update') && $user->can('permissions.view');
    }
}

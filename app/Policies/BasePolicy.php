<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

/**
 * Maps the standard CRUD abilities onto `{prefix}.{action}` permissions, so a
 * policy is usually three lines. Super admins short-circuit in Gate::before.
 */
abstract class BasePolicy
{
    use HandlesAuthorization;

    /** Permission prefix, e.g. "users" -> users.view, users.create, ... */
    abstract protected function prefix(): string;

    public function viewAny(User $user): bool
    {
        return $user->can($this->prefix().'.view');
    }

    public function view(User $user, Model $model): bool
    {
        return $user->can($this->prefix().'.view');
    }

    public function create(User $user): bool
    {
        return $user->can($this->prefix().'.create');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can($this->prefix().'.update');
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can($this->prefix().'.delete');
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->can($this->prefix().'.update');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->can($this->prefix().'.delete');
    }

    public function export(User $user): bool
    {
        return $user->canAny([$this->prefix().'.export', $this->prefix().'.view']);
    }

    public function import(User $user): bool
    {
        return $user->canAny([$this->prefix().'.import', $this->prefix().'.create']);
    }
}

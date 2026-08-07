<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    protected string $defaultSort = 'id';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'name', 'created_at'];

    public function model(): string
    {
        return Role::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn (Builder $q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->withCount(['permissions', 'users']);
    }

    public function allWithPermissionCounts(): Collection
    {
        return Role::query()
            ->withCount(['permissions', 'users'])
            ->orderBy('id')
            ->get();
    }

    public function permissionIdsFor(Role $role): array
    {
        return $role->permissions()->pluck('id')->all();
    }

    public function syncPermissions(Role $role, array $permissionIds): Role
    {
        $role->syncPermissions(
            \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->get()
        );

        return $role->refresh();
    }

    public function findByName(string $name): ?Role
    {
        return Role::query()->where('name', $name)->first();
    }

    /** The super role must never be renamed or deleted from the UI. */
    public function isProtected(Role $role): bool
    {
        return $role->name === config('admin.super_role');
    }
}

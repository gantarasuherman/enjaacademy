<?php

declare(strict_types=1);

namespace App\Services\Rbac;

use App\DataTransferObjects\RoleData;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Services\Menu\MenuCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
        private readonly MenuRepositoryInterface $menus,
        private readonly MenuCache $menuCache,
    ) {}

    public function create(RoleData $data): Role
    {
        return DB::transaction(function () use ($data) {
            /** @var Role $role */
            $role = $this->roles->create($data->toArray());

            if ($data->permissionIds !== []) {
                $this->roles->syncPermissions($role, $data->permissionIds);
            }

            $this->menus->syncRoleMenus($role->id, $data->menuIds);

            $this->menuCache->flush();

            return $role;
        });
    }

    public function update(Role $role, RoleData $data): Role
    {
        $this->guardProtected($role, renaming: $role->name !== $data->name);

        return DB::transaction(function () use ($role, $data) {
            $this->roles->update($role, $data->toArray());
            $this->roles->syncPermissions($role, $data->permissionIds);
            $this->menus->syncRoleMenus($role->id, $data->menuIds);

            $this->menuCache->flush();

            return $role->refresh();
        });
    }

    public function delete(Role $role): void
    {
        $this->guardProtected($role, renaming: true);

        if ($role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => __('This role is still assigned to :count user(s). Reassign them first.', [
                    'count' => $role->users()->count(),
                ]),
            ]);
        }

        $this->roles->delete($role);
        $this->menuCache->flush();
    }

    /** Permission matrix save: role -> permission ids. */
    public function syncPermissions(Role $role, array $permissionIds): Role
    {
        $role = $this->roles->syncPermissions($role, $permissionIds);

        $this->menuCache->flush();

        return $role;
    }

    /** Menu Access Matrix save: role -> menu ids. */
    public function syncMenus(Role $role, array $menuIds): void
    {
        $this->menus->syncRoleMenus($role->id, $menuIds);

        $this->menuCache->flush();
    }

    /**
     * Bulk save of the whole permission matrix in one request.
     *
     * @param  array<int, array<int, int>>  $matrix  roleId => [permissionId, ...]
     */
    public function saveMatrix(array $matrix): void
    {
        DB::transaction(function () use ($matrix) {
            foreach ($matrix as $roleId => $permissionIds) {
                $role = $this->roles->find((int) $roleId);

                if (! $role instanceof Role || $this->roles->isProtected($role)) {
                    continue; // the super role keeps its implicit access via Gate::before
                }

                $this->roles->syncPermissions($role, array_map('intval', (array) $permissionIds));
            }
        });

        $this->menuCache->flush();
    }

    /**
     * @param  array<int, array<int, int>>  $matrix  roleId => [menuId, ...]
     */
    public function saveMenuMatrix(array $matrix): void
    {
        DB::transaction(function () use ($matrix) {
            foreach ($matrix as $roleId => $menuIds) {
                $this->menus->syncRoleMenus((int) $roleId, array_map('intval', (array) $menuIds));
            }
        });

        $this->menuCache->flush();
    }

    private function guardProtected(Role $role, bool $renaming): void
    {
        if ($renaming && $this->roles->isProtected($role)) {
            throw ValidationException::withMessages([
                'name' => __('The :role role is protected and cannot be renamed or deleted.', [
                    'role' => $role->name,
                ]),
            ]);
        }
    }
}

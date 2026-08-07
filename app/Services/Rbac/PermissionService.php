<?php

declare(strict_types=1);

namespace App\Services\Rbac;

use App\DataTransferObjects\PermissionData;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Services\Menu\MenuCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function __construct(
        private readonly PermissionRepositoryInterface $permissions,
        private readonly MenuCache $menuCache,
    ) {}

    public function create(PermissionData $data): Permission
    {
        return DB::transaction(function () use ($data) {
            /** @var Permission $permission */
            $permission = $this->permissions->create($data->toArray());

            if ($data->roleIds !== []) {
                $permission->syncRoles(\Spatie\Permission\Models\Role::whereIn('id', $data->roleIds)->get());
            }

            $this->menuCache->flush();

            return $permission;
        });
    }

    public function update(Permission $permission, PermissionData $data): Permission
    {
        return DB::transaction(function () use ($permission, $data) {
            $this->permissions->update($permission, $data->toArray());
            $permission->syncRoles(\Spatie\Permission\Models\Role::whereIn('id', $data->roleIds)->get());

            $this->menuCache->flush();

            return $permission->refresh();
        });
    }

    public function delete(Permission $permission): void
    {
        // A menu pointing at a deleted permission would silently vanish for
        // everyone, so refuse and tell the admin where it is referenced.
        $usedByMenus = \App\Models\Menu::query()
            ->where('permission_name', $permission->name)
            ->pluck('title');

        if ($usedByMenus->isNotEmpty()) {
            throw ValidationException::withMessages([
                'permission' => __('This permission is used by menu(s): :menus. Update them first.', [
                    'menus' => $usedByMenus->implode(', '),
                ]),
            ]);
        }

        $this->permissions->delete($permission);
        $this->menuCache->flush();
    }

    /**
     * Generate the full `{module}.{action}` set — the one-click action that
     * makes a brand new module usable without touching code.
     *
     * @return Collection<int, Permission>
     */
    public function generateForModule(string $module, ?array $actions = null): Collection
    {
        $actions ??= config('admin.permission_actions', ['view', 'create', 'update', 'delete']);

        $created = $this->permissions->generateForModule($module, $actions);

        $this->menuCache->flush();

        return $created;
    }

    /** Data for the Role x Permission matrix screen. */
    public function matrix(): array
    {
        return [
            'grouped' => $this->permissions->groupedByModule(),
            'modules' => $this->permissions->modules(),
        ];
    }
}

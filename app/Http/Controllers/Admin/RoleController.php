<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\RoleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionMatrixRequest;
use App\Http\Requests\Admin\RoleRequest;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Services\Menu\MenuBuilder;
use App\Services\Rbac\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $service,
        private readonly RoleRepositoryInterface $roles,
        private readonly PermissionRepositoryInterface $permissions,
        private readonly MenuRepositoryInterface $menus,
        private readonly MenuBuilder $builder,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Role::class);

        return view('admin.roles.index', [
            'roles' => $this->roles->paginate($request->only(['search', 'sort', 'direction']), $this->perPage()),
            'superRole' => config('admin.super_role'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Role::class);

        return view('admin.roles.form', $this->formData(new Role));
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $role = $this->service->create(RoleData::fromRequest($request));

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role ":name" was created.', ['name' => $role->name]));
    }

    public function edit(Role $role): View
    {
        $this->authorize('update', $role);

        return view('admin.roles.form', $this->formData($role));
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->service->update($role, RoleData::fromRequest($request));

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role ":name" was updated.', ['name' => $role->name]));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $this->service->delete($role);

        return back()->with('success', __('Role ":name" was deleted.', ['name' => $role->name]));
    }

    /** Single-role permission editor. */
    public function permissions(Role $role): View
    {
        $this->authorize('managePermissions', Role::class);

        return view('admin.roles.permissions', [
            'role' => $role,
            'grouped' => $this->permissions->groupedByModule(),
            'assigned' => $this->roles->permissionIdsFor($role),
            'actions' => config('admin.permission_actions'),
            'isSuper' => $this->roles->isProtected($role),
        ]);
    }

    public function updatePermissions(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('managePermissions', Role::class);

        $validated = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $this->service->syncPermissions($role, array_map('intval', $validated['permissions'] ?? []));

        return back()->with('success', __('Permissions for ":name" were saved.', ['name' => $role->name]));
    }

    /** Full Role x Permission matrix. */
    public function matrix(): View
    {
        $this->authorize('managePermissions', Role::class);

        $roles = $this->roles->allWithPermissionCounts();

        return view('admin.roles.matrix', [
            'roles' => $roles,
            'grouped' => $this->permissions->groupedByModule(),
            'assigned' => $roles->mapWithKeys(fn (Role $role) => [
                $role->id => $this->roles->permissionIdsFor($role),
            ]),
            'superRole' => config('admin.super_role'),
        ]);
    }

    public function updateMatrix(PermissionMatrixRequest $request): RedirectResponse
    {
        $this->service->saveMatrix($request->matrix());

        return back()->with('success', __('Permission matrix saved.'));
    }

    private function formData(Role $role): array
    {
        return [
            'role' => $role,
            'grouped' => $this->permissions->groupedByModule(),
            'assigned' => $role->exists ? $this->roles->permissionIdsFor($role) : [],
            'tree' => $this->builder->adminTree(),
            'assignedMenus' => $role->exists ? $this->menus->menuIdsForRole($role->id) : [],
            'isSuper' => $role->exists && $this->roles->isProtected($role),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\PermissionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionRequest;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Services\Rbac\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function __construct(
        private readonly PermissionService $service,
        private readonly PermissionRepositoryInterface $permissions,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Permission::class);

        return view('admin.permissions.index', [
            'permissions' => $this->permissions->paginate($request->only(['search', 'module', 'sort', 'direction']), $this->perPage()),
            'modules' => $this->permissions->modules(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Permission::class);

        return view('admin.permissions.form', [
            'permission' => new Permission,
            'roles' => Role::orderBy('name')->get(),
            'assigned' => [],
            'modules' => $this->permissions->modules(),
            'actions' => config('admin.permission_actions'),
        ]);
    }

    public function store(PermissionRequest $request): RedirectResponse
    {
        $permission = $this->service->create(PermissionData::fromRequest($request));

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('Permission ":name" was created.', ['name' => $permission->name]));
    }

    public function edit(Permission $permission): View
    {
        $this->authorize('update', $permission);

        return view('admin.permissions.form', [
            'permission' => $permission,
            'roles' => Role::orderBy('name')->get(),
            'assigned' => $permission->roles->pluck('id')->all(),
            'modules' => $this->permissions->modules(),
            'actions' => config('admin.permission_actions'),
        ]);
    }

    public function update(PermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->service->update($permission, PermissionData::fromRequest($request));

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('Permission ":name" was updated.', ['name' => $permission->name]));
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->authorize('delete', $permission);

        $this->service->delete($permission);

        return back()->with('success', __('Permission ":name" was deleted.', ['name' => $permission->name]));
    }

    /**
     * Bulk-create the whole `{module}.{action}` set. This is the button that
     * lets an admin bring a brand new module online without a deployment.
     */
    public function generate(Request $request): RedirectResponse
    {
        $this->authorize('create', Permission::class);

        $validated = $request->validate([
            'module' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_\-]+$/'],
            'actions' => ['array'],
            'actions.*' => ['string', 'max:30'],
        ]);

        $created = $this->service->generateForModule(
            $validated['module'],
            $validated['actions'] ?? null,
        );

        return back()->with('success', __(':count permission(s) ready for module ":module".', [
            'count' => $created->count(),
            'module' => $validated['module'],
        ]));
    }
}

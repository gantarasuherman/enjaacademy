<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\MenuData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuAccessMatrixRequest;
use App\Http\Requests\Admin\MenuReorderRequest;
use App\Http\Requests\Admin\MenuRequest;
use App\Models\Menu;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Services\Menu\MenuBuilder;
use App\Services\Menu\MenuCache;
use App\Services\Menu\MenuService;
use App\Services\Rbac\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuController extends Controller
{
    public function __construct(
        private readonly MenuService $service,
        private readonly MenuBuilder $builder,
        private readonly MenuRepositoryInterface $menus,
        private readonly RoleService $roles,
        private readonly MenuCache $cache,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Menu::class);

        $position = $request->string('position')->toString() ?: null;

        return view('admin.menus.index', [
            'tree' => $this->builder->adminTree($position),
            'position' => $position,
            'positions' => config('admin.menu.positions'),
            'flat' => $this->menus->paginate($request->only(['search', 'position', 'type', 'is_active']), config('admin.per_page')),
            'view' => $request->string('view')->toString() ?: 'tree',
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Menu::class);

        return view('admin.menus.form', $this->formData(new Menu([
            'type' => 'menu',
            'position' => 'sidebar',
            'target' => '_self',
            'is_active' => true,
            'is_visible' => true,
            'is_sidebar' => true,
        ])));
    }

    public function store(MenuRequest $request): RedirectResponse
    {
        $menu = $this->service->create(MenuData::fromRequest($request));

        return redirect()
            ->route('admin.menus.index', ['position' => $menu->position])
            ->with('success', __('Menu ":title" was created.', ['title' => $menu->title]));
    }

    public function edit(Menu $menu): View
    {
        $this->authorize('update', $menu);

        $menu->load('roles:id');

        return view('admin.menus.form', $this->formData($menu));
    }

    public function update(MenuRequest $request, Menu $menu): RedirectResponse
    {
        $this->service->update($menu, MenuData::fromRequest($request));

        return redirect()
            ->route('admin.menus.index', ['position' => $menu->position])
            ->with('success', __('Menu ":title" was updated.', ['title' => $menu->title]));
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $this->authorize('delete', $menu);

        $this->service->delete($menu);

        return back()->with('success', __('Menu ":title" and its children were deleted.', ['title' => $menu->title]));
    }

    /** Drag & drop persistence — called by the Alpine tree component. */
    public function reorder(MenuReorderRequest $request): JsonResponse
    {
        $this->service->reorder($request->tree());

        return response()->json(['message' => __('Menu order saved.')]);
    }

    public function duplicate(Menu $menu): RedirectResponse
    {
        $this->authorize('create', Menu::class);

        $copy = $this->service->duplicate($menu);

        return redirect()
            ->route('admin.menus.edit', $copy)
            ->with('success', __('Menu duplicated — adjust and save.'));
    }

    /** Role x Menu matrix. */
    public function matrix(): View
    {
        $this->authorize('viewAny', Menu::class);

        $roles = Role::orderBy('id')->get();

        return view('admin.menus.matrix', [
            'roles' => $roles,
            'tree' => $this->builder->adminTree(),
            'assigned' => $roles->mapWithKeys(fn (Role $role) => [
                $role->id => $this->menus->menuIdsForRole($role->id),
            ]),
            'superRole' => config('admin.super_role'),
        ]);
    }

    public function updateMatrix(MenuAccessMatrixRequest $request): RedirectResponse
    {
        $this->roles->saveMenuMatrix($request->matrix());

        return back()->with('success', __('Menu access matrix saved.'));
    }

    public function clearCache(): RedirectResponse
    {
        $this->cache->flush();

        return back()->with('success', __('Menu cache cleared.'));
    }

    /** Shared payload for the create/edit form. */
    private function formData(Menu $menu): array
    {
        return [
            'menu' => $menu,
            'parents' => $this->service->flattenedForSelect($menu->exists ? $menu : null),
            'permissions' => Permission::orderBy('name')->pluck('name', 'name'),
            'roles' => Role::orderBy('name')->get(),
            'selectedRoles' => $menu->exists ? $menu->roles->pluck('id')->all() : [],
            'positions' => config('admin.menu.positions'),
            'types' => config('admin.menu.types'),
            'targets' => config('admin.menu.targets'),
            'badgeColors' => config('admin.menu.badge_colors'),
            'routeNames' => $this->availableRouteNames(),
        ];
    }

    /**
     * Named routes an admin may point a menu at. Internal framework routes and
     * anything with required parameters are filtered out to keep the picker
     * from offering links that cannot be generated.
     */
    private function availableRouteNames(): array
    {
        return collect(RouteFacade::getRoutes()->getRoutesByName())
            ->reject(fn ($route, string $name) => str_starts_with($name, 'sanctum.')
                || str_starts_with($name, 'ignition.')
                || str_starts_with($name, 'storage.')
                || str_contains($name, '.destroy')
                || str_contains($name, '.update')
                || str_contains($name, '.store'))
            ->filter(fn ($route) => in_array('GET', $route->methods(), true))
            ->keys()
            ->sort()
            ->values()
            ->all();
    }
}

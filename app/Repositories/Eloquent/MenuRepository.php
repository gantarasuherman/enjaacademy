<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Menu;
use App\Repositories\Contracts\MenuRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    /** The flat admin listing shows each row's parent, so preload it. */
    protected array $with = ['parent:id,title'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'title', 'slug', 'position', 'sort_order', 'created_at'];

    public function model(): string
    {
        return Menu::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->when($filters['position'] ?? null, fn (Builder $q, $p) => $q->where('position', $p))
            ->when($filters['type'] ?? null, fn (Builder $q, $t) => $q->where('type', $t))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['is_active']))
            ->when($filters['parent_id'] ?? null, fn (Builder $q, $id) => $q->where('parent_id', $id));
    }

    /**
     * One flat query for the whole position; the tree is assembled in PHP by
     * {@see \App\Services\Menu\MenuBuilder} so nesting costs no extra queries.
     */
    public function visibleFor(string $position): Collection
    {
        return Menu::query()
            ->active()
            ->position($position)
            ->with('roles:id')
            ->ordered()
            ->get();
    }

    public function treeForAdmin(?string $position = null): Collection
    {
        return Menu::query()
            ->roots()
            ->when($position, fn (Builder $q, $p) => $q->where('position', $p))
            ->with($this->nestedChildrenPaths())
            ->ordered()
            ->get();
    }

    /**
     * Eager-load paths for `children` down to the configured nesting limit.
     *
     * The admin views walk `$menu->children` directly, so the relation has to
     * be pre-loaded at every level — strict mode turns a missed level into an
     * exception rather than a silent N+1.
     *
     * @return array<int, string>
     */
    private function nestedChildrenPaths(): array
    {
        $paths = ['roles:id,name'];
        $path = 'children';

        for ($depth = 0; $depth < (int) config('admin.menu.max_depth', 5); $depth++) {
            $paths[] = $path;
            $paths[] = $path.'.roles:id,name';
            $path .= '.children';
        }

        return $paths;
    }

    public function allOrdered(): Collection
    {
        return Menu::query()->ordered()->get();
    }

    public function nextSortOrder(?int $parentId): int
    {
        return (int) Menu::query()
            ->where('parent_id', $parentId)
            ->max('sort_order') + 1;
    }

    public function applyPosition(int $menuId, ?int $parentId, int $sortOrder): void
    {
        // Straight update: observers still fire through the service's cache flush.
        Menu::query()->whereKey($menuId)->update([
            'parent_id' => $parentId,
            'sort_order' => $sortOrder,
            'updated_at' => now(),
        ]);
    }

    public function menuIdsForRole(int $roleId): array
    {
        return DB::table('menu_role')
            ->where('role_id', $roleId)
            ->pluck('menu_id')
            ->all();
    }

    public function syncRoleMenus(int $roleId, array $menuIds): void
    {
        DB::transaction(function () use ($roleId, $menuIds) {
            DB::table('menu_role')->where('role_id', $roleId)->delete();

            $rows = collect($menuIds)
                ->filter()
                ->unique()
                ->map(fn ($menuId) => [
                    'menu_id' => (int) $menuId,
                    'role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->all();

            if ($rows !== []) {
                DB::table('menu_role')->insert($rows);
            }
        });
    }

    public function findBySlug(string $slug): ?Menu
    {
        return Menu::query()->where('slug', $slug)->first();
    }
}

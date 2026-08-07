<?php

declare(strict_types=1);

namespace App\Services\Menu;

use App\DataTransferObjects\MenuData;
use App\Models\Menu;
use App\Repositories\Contracts\MenuRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MenuService
{
    public function __construct(
        private readonly MenuRepositoryInterface $menus,
        private readonly MenuCache $cache,
    ) {}

    public function create(MenuData $data): Menu
    {
        return DB::transaction(function () use ($data) {
            $attributes = $data->toArray();
            $attributes['sort_order'] ??= $this->menus->nextSortOrder($data->parentId);

            $menu = $this->menus->create($attributes);

            $menu->roles()->sync($data->roleIds);

            return $menu->refresh();
        });
    }

    public function update(Menu $menu, MenuData $data): Menu
    {
        $this->guardAgainstCycle($menu, $data->parentId);

        return DB::transaction(function () use ($menu, $data) {
            $this->menus->update($menu, $data->toArray());

            $menu->roles()->sync($data->roleIds);

            return $menu->refresh();
        });
    }

    public function delete(Menu $menu): void
    {
        DB::transaction(function () use ($menu) {
            // Children are cascade-deleted at the DB level; detach the matrix
            // rows first so no orphaned pivot survives a restore.
            $menu->roles()->detach();
            $this->menus->delete($menu);
        });
    }

    /**
     * Persist a drag & drop reorder.
     *
     * @param  array<int, array{id: int|string, parent_id?: int|string|null, children?: array}>  $tree
     */
    public function reorder(array $tree): void
    {
        DB::transaction(function () use ($tree) {
            $this->applyOrder($tree, null);
        });

        $this->cache->flush();
    }

    private function applyOrder(array $nodes, ?int $parentId, int $depth = 0): void
    {
        if ($depth > (int) config('admin.menu.max_depth', 5)) {
            throw ValidationException::withMessages([
                'tree' => __('Menu nesting is deeper than the configured maximum of :max levels.', [
                    'max' => config('admin.menu.max_depth'),
                ]),
            ]);
        }

        foreach (array_values($nodes) as $position => $node) {
            $id = (int) ($node['id'] ?? 0);

            if ($id <= 0) {
                continue;
            }

            $this->menus->applyPosition($id, $parentId, $position);

            if (! empty($node['children']) && is_array($node['children'])) {
                $this->applyOrder($node['children'], $id, $depth + 1);
            }
        }
    }

    /** Moving a menu under one of its own descendants would orphan the branch. */
    private function guardAgainstCycle(Menu $menu, ?int $parentId): void
    {
        if ($parentId === null || $parentId === $menu->parent_id) {
            return;
        }

        if (in_array($parentId, $menu->descendantIds(), true)) {
            throw ValidationException::withMessages([
                'parent_id' => __('A menu cannot be moved inside itself or one of its children.'),
            ]);
        }
    }

    /** @return Collection<int, Menu> */
    public function flattenedForSelect(?Menu $exclude = null): Collection
    {
        $excludeIds = $exclude ? $exclude->descendantIds() : [];

        return $this->menus->allOrdered()
            ->reject(fn (Menu $menu) => in_array($menu->id, $excludeIds, true))
            ->map(function (Menu $menu) {
                $menu->setAttribute('indented_title', str_repeat('— ', $menu->depth()).$menu->title);

                return $menu;
            })
            ->values();
    }

    /** Duplicating a menu branch, handy when cloning a module's navigation. */
    public function duplicate(Menu $menu, ?int $parentId = null): Menu
    {
        return DB::transaction(function () use ($menu, $parentId) {
            $copy = $menu->replicate(['slug']);
            $copy->parent_id = $parentId ?? $menu->parent_id;
            $copy->title = $menu->title.' (copy)';
            $copy->slug = $menu->slug.'-copy-'.uniqid();
            $copy->sort_order = $this->menus->nextSortOrder($copy->parent_id);
            $copy->save();

            $copy->roles()->sync($menu->roles->pluck('id')->all());

            foreach ($menu->children as $child) {
                $this->duplicate($child, $copy->id);
            }

            return $copy;
        });
    }
}

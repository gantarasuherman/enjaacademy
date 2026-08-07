<?php

declare(strict_types=1);

namespace App\Services\Menu;

use App\Models\Menu;
use App\Models\User;
use App\Repositories\Contracts\MenuRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Turns the `menus` table into the nested array the Blade layouts render.
 *
 * Pipeline: load active rows for the position -> filter by permission and by
 * the menu/role matrix -> build the parent/child tree -> drop empty branches
 * and stray dividers -> cache per (position, role signature).
 */
class MenuBuilder
{
    public function __construct(
        private readonly MenuRepositoryInterface $menus,
        private readonly MenuCache $cache,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function for(string $position, ?User $user = null): array
    {
        $user ??= auth()->user();

        if (! $user) {
            return [];
        }

        $signature = $this->signatureFor($user);

        return $this->cache->remember(
            $position,
            $signature,
            fn () => $this->build($position, $user),
        );
    }

    /**
     * Users with identical role sets share a cache entry. Users carrying
     * direct (non-role) permissions get their own entry, because their menu
     * can legitimately differ from everyone else with the same roles.
     */
    private function signatureFor(User $user): string
    {
        if ($user->isSuperAdmin()) {
            return 'super';
        }

        if ($user->permissions()->exists()) {
            return 'user:'.$user->getKey();
        }

        return 'roles:'.$user->roleSignature();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function build(string $position, User $user): array
    {
        $menus = $this->menus->visibleFor($position);

        $roleIds = $user->roles->pluck('id')->all();

        $allowed = $menus->filter(fn (Menu $menu) => $this->isAllowed($menu, $user, $roleIds));

        $tree = $this->toTree($allowed, null);

        return $this->prune($tree);
    }

    /**
     * A menu is visible when the permission check passes *and* the menu/role
     * matrix either has no opinion or explicitly includes one of the user's
     * roles. Super admins bypass both checks.
     */
    private function isAllowed(Menu $menu, User $user, array $roleIds): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (filled($menu->permission_name) && ! $user->can($menu->permission_name)) {
            return false;
        }

        $matrixRoleIds = $menu->roles->pluck('id')->all();

        if ($matrixRoleIds !== [] && array_intersect($matrixRoleIds, $roleIds) === []) {
            return false;
        }

        return true;
    }

    /**
     * @param  Collection<int, Menu>  $menus
     * @return array<int, array<string, mixed>>
     */
    private function toTree(Collection $menus, ?int $parentId, int $depth = 0): array
    {
        if ($depth > (int) config('admin.menu.max_depth', 5)) {
            return [];
        }

        return $menus
            ->where('parent_id', $parentId)
            ->sortBy([['sort_order', 'asc'], ['id', 'asc']])
            ->values()
            ->map(fn (Menu $menu) => [
                'id' => $menu->id,
                'title' => $menu->title,
                'slug' => $menu->slug,
                'icon' => $menu->icon,
                'type' => $menu->type,
                'url' => $menu->resolveUrl(),
                'route_name' => $menu->route_name,
                'target' => $menu->target,
                'badge' => $menu->badge,
                'badge_color' => $menu->badge_color,
                'description' => $menu->description,
                'depth' => $depth,
                'children' => $this->toTree($menus, $menu->id, $depth + 1),
            ])
            ->all();
    }

    /**
     * Removes branches that ended up empty after filtering: a parent whose
     * children were all hidden is itself pointless, and leading/trailing or
     * doubled dividers look broken.
     *
     * @param  array<int, array<string, mixed>>  $tree
     * @return array<int, array<string, mixed>>
     */
    private function prune(array $tree): array
    {
        $kept = [];

        foreach ($tree as $node) {
            $node['children'] = $this->prune($node['children']);

            $isBranchOnly = $node['children'] === []
                && $node['type'] === 'menu'
                && $node['url'] === '#'
                && blank($node['route_name']);

            if ($isBranchOnly) {
                continue;
            }

            // A header with nothing after it, or a duplicate divider.
            if (in_array($node['type'], ['divider', 'header'], true)) {
                $previous = end($kept);

                if ($kept === [] || ($previous && in_array($previous['type'], ['divider', 'header'], true))) {
                    if ($node['type'] === 'divider') {
                        continue;
                    }
                }
            }

            $kept[] = $node;
        }

        // Trailing divider/header with nothing beneath it.
        while ($kept !== [] && in_array(end($kept)['type'], ['divider', 'header'], true)) {
            array_pop($kept);
        }

        return $kept;
    }

    /**
     * Full tree with no permission filtering — used by the admin menu manager
     * and the Menu Access Matrix.
     *
     * @return Collection<int, Menu>
     */
    public function adminTree(?string $position = null): Collection
    {
        return $this->menus->treeForAdmin($position);
    }
}

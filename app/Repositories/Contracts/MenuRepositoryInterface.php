<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Menu;
use Illuminate\Support\Collection;

interface MenuRepositoryInterface extends RepositoryInterface
{
    /** Active menus for a position, with the role matrix eager loaded. */
    public function visibleFor(string $position): Collection;

    /** Every menu (including hidden ones) as a nested tree, for the admin UI. */
    public function treeForAdmin(?string $position = null): Collection;

    public function allOrdered(): Collection;

    public function nextSortOrder(?int $parentId): int;

    public function applyPosition(int $menuId, ?int $parentId, int $sortOrder): void;

    /** Menu ids currently granted to a role, for the Menu Access Matrix. */
    public function menuIdsForRole(int $roleId): array;

    public function syncRoleMenus(int $roleId, array $menuIds): void;

    public function findBySlug(string $slug): ?Menu;
}

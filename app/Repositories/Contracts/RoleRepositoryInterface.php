<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

interface RoleRepositoryInterface extends RepositoryInterface
{
    public function allWithPermissionCounts(): Collection;

    /** Permission ids granted to a role — the row of the permission matrix. */
    public function permissionIdsFor(Role $role): array;

    public function syncPermissions(Role $role, array $permissionIds): Role;

    public function findByName(string $name): ?Role;

    public function isProtected(Role $role): bool;
}

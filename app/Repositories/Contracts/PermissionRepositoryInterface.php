<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

interface PermissionRepositoryInterface extends RepositoryInterface
{
    /** All permissions keyed by module ("users" => [users.view, users.create ...]). */
    public function groupedByModule(): Collection;

    public function modules(): Collection;

    public function findByName(string $name): ?Permission;

    /** Bulk-create `{module}.{action}` permissions, skipping ones that exist. */
    public function generateForModule(string $module, array $actions): Collection;
}

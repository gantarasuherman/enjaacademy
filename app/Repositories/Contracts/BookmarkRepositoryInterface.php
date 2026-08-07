<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookmarkRepositoryInterface extends RepositoryInterface
{
    /** Add or remove; returns true when the item ends up bookmarked. */
    public function toggle(User $user, string $type, int|string $id, ?string $label = null): bool;

    public function exists(User $user, string $type, int|string $id): bool;

    public function paginateFor(User $user, array $filters, int $perPage): LengthAwarePaginator;
}

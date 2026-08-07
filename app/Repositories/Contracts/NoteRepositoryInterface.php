<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NoteRepositoryInterface extends RepositoryInterface
{
    public function paginateFor(User $user, array $filters, int $perPage): LengthAwarePaginator;

    public function forSubject(User $user, string $type, int|string $id);
}

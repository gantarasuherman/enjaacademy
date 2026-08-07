<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Bookmark;
use App\Models\User;
use App\Repositories\Contracts\BookmarkRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class BookmarkRepository extends BaseRepository implements BookmarkRepositoryInterface
{
    protected string $defaultSort = 'created_at';

    public function model(): string
    {
        return Bookmark::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['user_id'] ?? null, fn (Builder $q, $id) => $q->where('user_id', $id))
            ->when($filters['type'] ?? null, fn (Builder $q, $t) => $q->where('bookmarkable_type', $t));
    }

    public function toggle(User $user, string $type, int|string $id, ?string $label = null): bool
    {
        $existing = Bookmark::query()
            ->where('user_id', $user->id)
            ->where('bookmarkable_type', $type)
            ->where('bookmarkable_id', $id)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        Bookmark::create([
            'user_id' => $user->id,
            'bookmarkable_type' => $type,
            'bookmarkable_id' => $id,
            'label' => $label,
        ]);

        return true;
    }

    public function exists(User $user, string $type, int|string $id): bool
    {
        return Bookmark::query()
            ->where('user_id', $user->id)
            ->where('bookmarkable_type', $type)
            ->where('bookmarkable_id', $id)
            ->exists();
    }

    public function paginateFor(User $user, array $filters, int $perPage): LengthAwarePaginator
    {
        return Bookmark::query()
            ->where('user_id', $user->id)
            ->when($filters['type'] ?? null, fn (Builder $q, $t) => $q->where('bookmarkable_type', $t))
            ->with('bookmarkable')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}

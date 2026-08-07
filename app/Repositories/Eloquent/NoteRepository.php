<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Note;
use App\Models\User;
use App\Repositories\Contracts\NoteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class NoteRepository extends BaseRepository implements NoteRepositoryInterface
{
    protected string $defaultSort = 'updated_at';

    protected array $sortable = ['id', 'title', 'created_at', 'updated_at'];

    public function model(): string
    {
        return Note::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->when($filters['user_id'] ?? null, fn (Builder $q, $id) => $q->where('user_id', $id));
    }

    public function paginateFor(User $user, array $filters, int $perPage): LengthAwarePaginator
    {
        return Note::query()
            ->where('user_id', $user->id)
            ->search($filters['search'] ?? null)
            ->with('notable')
            ->pinnedFirst()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function forSubject(User $user, string $type, int|string $id): Collection
    {
        return Note::query()
            ->where('user_id', $user->id)
            ->where('notable_type', $type)
            ->where('notable_id', $id)
            ->pinnedFirst()
            ->get();
    }
}

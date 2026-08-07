<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\FlashcardDeck;
use App\Models\User;
use App\Repositories\Contracts\FlashcardDeckRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FlashcardDeckRepository extends BaseRepository implements FlashcardDeckRepositoryInterface
{
    protected array $with = ['module:id,name,slug,color'];

    protected string $defaultSort = 'created_at';

    protected array $sortable = ['id', 'name', 'created_at'];

    public function model(): string
    {
        return FlashcardDeck::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->when($filters['module_id'] ?? null, fn (Builder $q, $id) => $q->where('learning_module_id', $id))
            ->when(isset($filters['is_public']) && $filters['is_public'] !== '', fn (Builder $q) => $q->where('is_public', (bool) $filters['is_public']))
            ->when(($filters['scope'] ?? null) === 'system', fn (Builder $q) => $q->whereNull('user_id'))
            ->when(($filters['scope'] ?? null) === 'mine' && isset($filters['user_id']), fn (Builder $q) => $q->where('user_id', $filters['user_id']))
            ->withCount('cards');
    }

    public function findBySlug(string $slug): ?FlashcardDeck
    {
        return FlashcardDeck::query()->with('cards')->where('slug', $slug)->first();
    }

    public function visibleFor(?User $user): Collection
    {
        return FlashcardDeck::query()
            ->active()
            ->visibleTo($user)
            ->withCount('cards')
            ->with('module:id,name,color')
            ->orderByDesc('created_at')
            ->get();
    }

    public function forSelect(): Collection
    {
        return FlashcardDeck::query()->orderBy('name')->get(['id', 'name']);
    }
}

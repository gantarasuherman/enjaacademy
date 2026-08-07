<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Language;
use App\Repositories\Contracts\LanguageRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{
    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'name', 'code', 'sort_order', 'created_at'];

    public function model(): string
    {
        return Language::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['is_active']))
            ->withCount('modules');
    }

    public function active(): Collection
    {
        return Language::query()->active()->orderBy('sort_order')->get();
    }

    public function findBySlug(string $slug): ?Language
    {
        return Language::query()->where('slug', $slug)->first();
    }

    public function withModuleCounts(): Collection
    {
        return Language::query()
            ->active()
            ->withCount(['modules' => fn (Builder $q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();
    }
}

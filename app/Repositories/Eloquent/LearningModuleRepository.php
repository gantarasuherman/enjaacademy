<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\LearningModule;
use App\Models\User;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LearningModuleRepository extends BaseRepository implements LearningModuleRepositoryInterface
{
    protected array $with = ['language:id,name,slug,flag,color'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'name', 'sort_order', 'created_at'];

    public function model(): string
    {
        return LearningModule::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->forLanguage($filters['language'] ?? null)
            ->when($filters['content_type'] ?? null, fn (Builder $q, $t) => $q->where('content_type', $t))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['is_active']))
            ->withCount('lessons');
    }

    public function findBySlug(string $slug): ?LearningModule
    {
        return LearningModule::query()->with('language')->where('slug', $slug)->first();
    }

    /**
     * Permission-aware listing: a module is offered only when the user holds
     * `{permission_prefix}.view`, so admins gate content without touching code.
     */
    public function accessibleFor(User $user): Collection
    {
        return LearningModule::query()
            ->active()
            ->with('language')
            ->withCount(['lessons' => fn (Builder $q) => $q->where('is_published', true)])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (LearningModule $module) => $user->can($module->permission('view')))
            ->groupBy(fn (LearningModule $module) => $module->language->name);
    }

    public function featured(int $limit = 6): Collection
    {
        return LearningModule::query()
            ->active()
            ->where('is_featured', true)
            ->with('language')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }

    public function withLessonCounts(): Collection
    {
        return LearningModule::query()
            ->active()
            ->withCount(['lessons', 'quizzes'])
            ->with('language:id,name,slug')
            ->orderBy('sort_order')
            ->get();
    }

    public function forSelect(): Collection
    {
        return LearningModule::query()
            ->with('language:id,name')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'language_id']);
    }
}

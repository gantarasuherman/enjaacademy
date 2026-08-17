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
     * Each module is also annotated with `is_enrolled` — enrollment is a
     * record, not a gate, so it never affects which modules appear here.
     */
    public function accessibleFor(User $user): Collection
    {
        $enrolledIds = $user->enrollments()->pluck('learning_module_id')->all();

        return LearningModule::query()
            ->active()
            ->with('language')
            ->withCount(['lessons' => fn (Builder $q) => $q->where('is_published', true)])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (LearningModule $module) => $user->can($module->permission('view')))
            ->each(fn (LearningModule $module) => $module->setAttribute(
                'is_enrolled',
                in_array($module->id, $enrolledIds, true),
            ))
            ->groupBy(fn (LearningModule $module) => $module->language->name);
    }

    /**
     * Deliberately no `User`/permission check — this feeds the pre-login
     * catalog on the landing page, so only publicly-safe fields matter
     * (`ModuleResource` never exposes lesson content, just metadata/counts).
     */
    public function publicCatalog(array $filters = []): Collection
    {
        return LearningModule::query()
            ->active()
            ->forLanguage($filters['language'] ?? null)
            ->when($filters['content_type'] ?? null, fn (Builder $q, $t) => $q->where('content_type', $t))
            ->when(
                $filters['level'] ?? null,
                fn (Builder $q, $level) => $q->whereHas('lessons', fn (Builder $l) => $l->where('level', $level)),
            )
            ->with('language')
            ->withCount(['lessons' => fn (Builder $q) => $q->where('is_published', true)])
            ->orderBy('sort_order')
            ->get();
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

    public function forSelect(?string $contentType = null): Collection
    {
        return LearningModule::query()
            ->when($contentType, fn (Builder $q) => $q->where('content_type', $contentType))
            ->with('language:id,name,slug')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'language_id', 'content_type']);
    }
}

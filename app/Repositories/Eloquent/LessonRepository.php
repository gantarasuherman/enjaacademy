<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Lesson;
use App\Repositories\Contracts\LessonRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LessonRepository extends BaseRepository implements LessonRepositoryInterface
{
    protected array $with = ['module:id,name,slug,color,icon,content_type'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'title', 'level', 'sort_order', 'created_at'];

    public function model(): string
    {
        return Lesson::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->forModule($filters['module'] ?? null)
            ->level($filters['level'] ?? null)
            ->when(isset($filters['is_published']) && $filters['is_published'] !== '', fn (Builder $q) => $q->where('is_published', (bool) $filters['is_published']))
            ->when(
                $filters['module_content_type'] ?? null,
                fn (Builder $q, $type) => $q->whereHas('module', fn (Builder $m) => $m->where('content_type', $type)),
            )
            ->withCount('items');
    }

    public function findBySlug(string $slug): ?Lesson
    {
        return Lesson::query()
            ->with(['module.language', 'items' => fn ($q) => $q->where('is_active', true)])
            ->where('slug', $slug)
            ->first();
    }

    public function publishedForModule(int $moduleId): Collection
    {
        return Lesson::query()
            ->published()
            ->where('learning_module_id', $moduleId)
            ->withCount('items')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Previous / next lesson inside the same module, for the lesson player.
     *
     * @return array{previous: ?Lesson, next: ?Lesson}
     */
    public function neighbours(Lesson $lesson): array
    {
        $base = fn () => Lesson::query()
            ->published()
            ->where('learning_module_id', $lesson->learning_module_id);

        return [
            'previous' => $base()
                ->where('sort_order', '<', $lesson->sort_order)
                ->orderByDesc('sort_order')
                ->first(),
            'next' => $base()
                ->where('sort_order', '>', $lesson->sort_order)
                ->orderBy('sort_order')
                ->first(),
        ];
    }

    public function levelsForModule(int $moduleId): Collection
    {
        return Lesson::query()
            ->where('learning_module_id', $moduleId)
            ->whereNotNull('level')
            ->distinct()
            ->orderBy('level')
            ->pluck('level');
    }

    public function forSelect(?int $moduleId = null): Collection
    {
        return Lesson::query()
            ->when($moduleId, fn (Builder $q) => $q->where('learning_module_id', $moduleId))
            ->orderBy('sort_order')
            ->get(['id', 'title', 'learning_module_id']);
    }
}

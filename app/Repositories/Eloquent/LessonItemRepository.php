<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\LessonItem;
use App\Repositories\Contracts\LessonItemRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LessonItemRepository extends BaseRepository implements LessonItemRepositoryInterface
{
    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'term', 'sort_order', 'created_at'];

    public function model(): string
    {
        return LessonItem::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->when($filters['lesson_id'] ?? null, fn (Builder $q, $id) => $q->where('lesson_id', $id))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['is_active']));
    }

    public function forLesson(int $lessonId): Collection
    {
        return LessonItem::query()
            ->where('lesson_id', $lessonId)
            ->orderBy('sort_order')
            ->get();
    }

    public function randomForModule(int $moduleId, int $limit = 20): Collection
    {
        return LessonItem::query()
            ->active()
            ->whereHas('lesson', fn (Builder $q) => $q->where('learning_module_id', $moduleId)->where('is_published', true))
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function insertMany(int $lessonId, array $rows): int
    {
        $order = $this->nextSortOrder($lessonId);
        $now = now();

        $payload = collect($rows)
            ->filter(fn (array $row) => filled($row['term'] ?? null))
            ->values()
            ->map(fn (array $row, int $i) => [
                'lesson_id' => $lessonId,
                'term' => $row['term'],
                'reading' => $row['reading'] ?? null,
                'romaji' => $row['romaji'] ?? null,
                'meaning' => $row['meaning'] ?? null,
                'example' => $row['example'] ?? null,
                'example_meaning' => $row['example_meaning'] ?? null,
                'extra' => isset($row['extra']) ? json_encode($row['extra']) : null,
                'sort_order' => $order + $i,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        if ($payload === []) {
            return 0;
        }

        LessonItem::query()->insert($payload);

        return count($payload);
    }

    public function nextSortOrder(int $lessonId): int
    {
        return (int) LessonItem::query()->where('lesson_id', $lessonId)->max('sort_order') + 1;
    }
}

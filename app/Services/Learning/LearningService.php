<?php

declare(strict_types=1);

namespace App\Services\Learning;

use App\DataTransferObjects\LearningModuleData;
use App\DataTransferObjects\LessonData;
use App\Models\LearningModule;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Repositories\Contracts\LessonItemRepositoryInterface;
use App\Repositories\Contracts\LessonRepositoryInterface;
use App\Services\Rbac\PermissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LearningService
{
    public function __construct(
        private readonly LearningModuleRepositoryInterface $modules,
        private readonly LessonRepositoryInterface $lessons,
        private readonly LessonItemRepositoryInterface $items,
        private readonly PermissionService $permissions,
    ) {}

    public function createModule(LearningModuleData $data): LearningModule
    {
        return DB::transaction(function () use ($data) {
            /** @var LearningModule $module */
            $module = $this->modules->create($data->toArray());

            // One tick in the form and the module is fully gated — this is
            // what makes "add a module without deploying" actually work.
            if ($data->generatePermissions) {
                $this->permissions->generateForModule($module->permission_prefix);
            }

            return $module;
        });
    }

    public function updateModule(LearningModule $module, LearningModuleData $data): LearningModule
    {
        return DB::transaction(function () use ($module, $data) {
            $this->modules->update($module, $data->toArray());

            if ($data->generatePermissions) {
                $this->permissions->generateForModule($module->permission_prefix);
            }

            return $module->refresh();
        });
    }

    public function deleteModule(LearningModule $module): void
    {
        $this->modules->delete($module);
    }

    public function createLesson(LessonData $data): Lesson
    {
        return DB::transaction(function () use ($data) {
            $attributes = $data->toArray();
            $attributes['created_by'] = auth()->id();
            $attributes['cover_image'] = $data->coverImage?->store('lessons/covers', 'public');
            $attributes['audio_path'] = $data->audio?->store('lessons/audio', 'public');

            /** @var Lesson $lesson */
            $lesson = $this->lessons->create($attributes);

            $this->syncItems($lesson, $data->items);

            return $lesson->refresh();
        });
    }

    public function updateLesson(Lesson $lesson, LessonData $data): Lesson
    {
        return DB::transaction(function () use ($lesson, $data) {
            $attributes = $data->toArray();

            if ($data->coverImage) {
                $this->deleteFile($lesson->cover_image);
                $attributes['cover_image'] = $data->coverImage->store('lessons/covers', 'public');
            }

            if ($data->audio) {
                $this->deleteFile($lesson->audio_path);
                $attributes['audio_path'] = $data->audio->store('lessons/audio', 'public');
            }

            // Keep the original publish date once set.
            if ($lesson->published_at) {
                $attributes['published_at'] = $data->isPublished ? $lesson->published_at : null;
            }

            $this->lessons->update($lesson, $attributes);

            $this->syncItems($lesson, $data->items);

            return $lesson->refresh();
        });
    }

    public function deleteLesson(Lesson $lesson): void
    {
        $this->lessons->delete($lesson);
    }

    /**
     * Inline item editor: rows carrying an `id` are updated, new rows are
     * inserted, and anything missing from the payload is removed.
     */
    private function syncItems(Lesson $lesson, array $items): void
    {
        if ($items === []) {
            return;
        }

        $kept = [];

        foreach (array_values($items) as $index => $row) {
            if (blank($row['term'] ?? null)) {
                continue;
            }

            /** @var LessonItem $item */
            $item = LessonItem::updateOrCreate(
                ['id' => $row['id'] ?? null, 'lesson_id' => $lesson->id],
                [
                    'term' => $row['term'],
                    'reading' => $row['reading'] ?? null,
                    'romaji' => $row['romaji'] ?? null,
                    'meaning' => $row['meaning'] ?? null,
                    'example' => $row['example'] ?? null,
                    'example_meaning' => $row['example_meaning'] ?? null,
                    'extra' => ! empty($row['extra']) ? $row['extra'] : null,
                    'sort_order' => $index,
                    'is_active' => filter_var($row['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
                ],
            );

            $kept[] = $item->id;
        }

        $lesson->items()->whereNotIn('id', $kept ?: [0])->delete();
    }

    public function reorderLessons(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            foreach (array_values($ids) as $position => $id) {
                Lesson::query()->whereKey($id)->update(['sort_order' => $position]);
            }
        });
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

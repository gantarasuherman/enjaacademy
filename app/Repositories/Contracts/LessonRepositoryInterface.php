<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Lesson;
use Illuminate\Support\Collection;

interface LessonRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Lesson;

    public function publishedForModule(int $moduleId): Collection;

    public function neighbours(Lesson $lesson): array;

    public function levelsForModule(int $moduleId): Collection;

    public function forSelect(?int $moduleId = null): Collection;
}

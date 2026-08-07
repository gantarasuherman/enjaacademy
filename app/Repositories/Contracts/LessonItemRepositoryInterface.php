<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface LessonItemRepositoryInterface extends RepositoryInterface
{
    public function forLesson(int $lessonId): Collection;

    public function randomForModule(int $moduleId, int $limit = 20): Collection;

    /** Bulk insert used by the CSV importer. */
    public function insertMany(int $lessonId, array $rows): int;

    public function nextSortOrder(int $lessonId): int;
}

<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\LearningModule;
use App\Models\User;
use Illuminate\Support\Collection;

interface LearningModuleRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?LearningModule;

    /** Active modules the user is allowed to open, grouped by language. */
    public function accessibleFor(User $user): Collection;

    public function featured(int $limit = 6): Collection;

    public function withLessonCounts(): Collection;

    public function forSelect(): Collection;
}

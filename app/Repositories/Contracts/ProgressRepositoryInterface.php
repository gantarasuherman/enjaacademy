<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\UserProgress;
use App\Models\UserStat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ProgressRepositoryInterface extends RepositoryInterface
{
    public function statFor(User $user): UserStat;

    public function touch(User $user, Model $trackable, int $percent, ?string $status = null): UserProgress;

    public function completedLessonIds(User $user): array;

    public function moduleCompletion(User $user): Collection;

    /** XP earned per day for the activity chart. */
    public function xpPerDay(User $user, int $days = 30): Collection;

    public function recentActivity(User $user, int $limit = 10): Collection;
}

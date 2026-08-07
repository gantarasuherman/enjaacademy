<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface AchievementRepositoryInterface extends RepositoryInterface
{
    public function active(): Collection;

    /** Every active achievement annotated with this user's unlock state. */
    public function withUnlockStateFor(User $user): Collection;

    public function unlockedCount(User $user): int;
}

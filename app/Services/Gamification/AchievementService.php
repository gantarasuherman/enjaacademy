<?php

declare(strict_types=1);

namespace App\Services\Gamification;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserStat;
use App\Models\XpLog;
use App\Repositories\Contracts\AchievementRepositoryInterface;

class AchievementService
{
    public function __construct(private readonly AchievementRepositoryInterface $achievements) {}

    /**
     * Re-evaluates every criteria-driven achievement against the user's
     * current counters and unlocks the ones that now qualify.
     *
     * @return array<int, Achievement> newly unlocked
     */
    public function evaluate(User $user, ?UserStat $stat = null): array
    {
        $stat ??= $user->stat()->firstOrCreate([]);

        $alreadyUnlocked = $user->achievements()
            ->wherePivotNotNull('unlocked_at')
            ->pluck('achievements.id')
            ->all();

        $unlocked = [];

        foreach ($this->achievements->active() as $achievement) {
            if (in_array($achievement->id, $alreadyUnlocked, true)) {
                continue;
            }

            $column = $achievement->statColumn();

            if ($column === null) {
                continue; // 'manual' achievements are granted by an admin
            }

            $current = (int) ($stat->{$column} ?? 0);

            if ($current >= $achievement->criteria_value) {
                $this->unlock($user, $achievement);
                $unlocked[] = $achievement;
            } else {
                // Keep partial progress so the UI can show a progress bar.
                $user->achievements()->syncWithoutDetaching([
                    $achievement->id => ['progress' => $current],
                ]);
            }
        }

        return $unlocked;
    }

    public function unlock(User $user, Achievement $achievement): void
    {
        $user->achievements()->syncWithoutDetaching([
            $achievement->id => [
                'progress' => $achievement->criteria_value,
                'unlocked_at' => now(),
            ],
        ]);

        if ($achievement->xp_reward > 0) {
            // Written directly rather than through ProgressService to avoid a
            // recursive evaluate() call while we are already inside one.
            XpLog::create([
                'user_id' => $user->id,
                'source_type' => $achievement->getMorphClass(),
                'source_id' => $achievement->id,
                'amount' => $achievement->xp_reward,
                'reason' => __('Achievement: :name', ['name' => $achievement->name]),
            ]);

            $stat = $user->stat()->firstOrCreate([]);
            $stat->xp_total += $achievement->xp_reward;
            $stat->level = UserStat::levelForXp((int) $stat->xp_total);
            $stat->save();
        }
    }

    public function revoke(User $user, Achievement $achievement): void
    {
        $user->achievements()->detach($achievement->id);
    }
}

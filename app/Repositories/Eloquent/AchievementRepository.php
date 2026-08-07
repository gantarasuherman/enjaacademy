<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Achievement;
use App\Models\User;
use App\Repositories\Contracts\AchievementRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AchievementRepository extends BaseRepository implements AchievementRepositoryInterface
{
    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'name', 'sort_order', 'criteria_value'];

    public function model(): string
    {
        return Achievement::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->when($filters['criteria_type'] ?? null, fn (Builder $q, $t) => $q->where('criteria_type', $t))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['is_active']))
            ->withCount('users');
    }

    public function active(): Collection
    {
        return Achievement::query()->active()->orderBy('sort_order')->get();
    }

    public function withUnlockStateFor(User $user): Collection
    {
        return Achievement::query()
            ->active()
            ->with(['users' => fn ($q) => $q->whereKey($user->id)])
            ->orderBy('sort_order')
            ->get()
            ->map(function (Achievement $achievement) {
                $pivot = $achievement->users->first()?->pivot;

                $achievement->setAttribute('is_unlocked', $pivot?->unlocked_at !== null);
                $achievement->setAttribute('unlocked_at', $pivot?->unlocked_at);
                $achievement->setAttribute('user_progress', (int) ($pivot->progress ?? 0));

                return $achievement;
            });
    }

    public function unlockedCount(User $user): int
    {
        return $user->achievements()->wherePivotNotNull('unlocked_at')->count();
    }
}

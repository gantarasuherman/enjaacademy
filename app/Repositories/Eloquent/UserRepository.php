<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected array $with = ['roles:id,name'];

    protected string $defaultSort = 'created_at';

    protected array $sortable = ['id', 'name', 'email', 'created_at', 'last_login_at'];

    public function model(): string
    {
        return User::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->withRole($filters['role'] ?? null)
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['is_active']))
            ->when(($filters['verified'] ?? null) === '1', fn (Builder $q) => $q->whereNotNull('email_verified_at'))
            ->when(($filters['verified'] ?? null) === '0', fn (Builder $q) => $q->whereNull('email_verified_at'))
            ->when(($filters['trashed'] ?? null) === '1', fn (Builder $q) => $q->onlyTrashed());
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function countByRole(): Collection
    {
        return DB::table('roles')
            ->leftJoin('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.name', DB::raw('COUNT(model_has_roles.model_id) as total'))
            ->groupBy('roles.id', 'roles.name')
            ->orderByDesc('total')
            ->get();
    }

    public function recentlyRegistered(int $limit = 5): Collection
    {
        return User::query()->with('roles:id,name')->latest()->limit($limit)->get();
    }

    public function leaderboard(int $limit = 10): Collection
    {
        return User::query()
            ->active()
            ->join('user_stats', 'user_stats.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.avatar', 'user_stats.xp_total', 'user_stats.level')
            ->orderByDesc('user_stats.xp_total')
            ->limit($limit)
            ->get();
    }

    public function registrationsPerMonth(int $months = 12): Collection
    {
        return User::query()
            ->where('created_at', '>=', now()->subMonths($months - 1)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');
    }
}

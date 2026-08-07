<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AuditLogRepository extends BaseRepository implements AuditLogRepositoryInterface
{
    protected array $with = ['user:id,name,email,avatar'];

    protected string $defaultSort = 'created_at';

    protected array $sortable = ['id', 'event', 'created_at'];

    public function model(): string
    {
        return AuditLog::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->event($filters['event'] ?? null)
            ->forUser($filters['user_id'] ?? null)
            ->forType($filters['auditable_type'] ?? null)
            ->between($filters['from'] ?? null, $filters['to'] ?? null);
    }

    public function events(): Collection
    {
        return AuditLog::query()->distinct()->orderBy('event')->pluck('event');
    }

    public function auditableTypes(): Collection
    {
        return AuditLog::query()
            ->whereNotNull('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type');
    }

    public function latestFor(string $type, int|string $id, int $limit = 20): Collection
    {
        return AuditLog::query()
            ->where('auditable_type', $type)
            ->where('auditable_id', $id)
            ->with('user:id,name')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function prune(int $days): int
    {
        return AuditLog::query()
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }
}

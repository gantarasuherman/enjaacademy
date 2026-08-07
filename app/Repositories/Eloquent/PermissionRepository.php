<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    protected string $defaultSort = 'name';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'name', 'created_at'];

    public function model(): string
    {
        return Permission::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn (Builder $q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($filters['module'] ?? null, fn (Builder $q, $m) => $q->where('name', 'like', "{$m}.%"))
            ->withCount('roles');
    }

    public function groupedByModule(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->groupBy(fn (Permission $permission) => Str::before($permission->name, '.'));
    }

    public function modules(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->pluck('name')
            ->map(fn (string $name) => Str::before($name, '.'))
            ->unique()
            ->values();
    }

    public function findByName(string $name): ?Permission
    {
        return Permission::query()->where('name', $name)->first();
    }

    public function generateForModule(string $module, array $actions): Collection
    {
        $module = Str::of($module)->trim()->lower()->replace(' ', '_')->toString();

        return collect($actions)
            ->map(fn (string $action) => Permission::firstOrCreate([
                'name' => "{$module}.{$action}",
                'guard_name' => config('auth.defaults.guard', 'web'),
            ]))
            ->values();
    }
}

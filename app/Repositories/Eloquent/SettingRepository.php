<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected array $sortable = ['id', 'key', 'group', 'sort_order'];

    public function model(): string
    {
        return Setting::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['group'] ?? null, fn (Builder $q, $g) => $q->where('group', $g))
            ->when($filters['search'] ?? null, fn (Builder $q, $s) => $q->where('key', 'like', "%{$s}%"));
    }

    public function pairs(?string $group = null): Collection
    {
        return Setting::query()
            ->when($group, fn (Builder $q) => $q->where('group', $group))
            ->get()
            ->mapWithKeys(fn (Setting $s) => [$s->key => $s->typedValue()]);
    }

    public function put(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        if (in_array($type, ['json', 'array'], true) && ! is_string($value)) {
            $value = json_encode($value);
        }

        if ($type === 'bool') {
            $value = $value ? '1' : '0';
        }

        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => is_scalar($value) || $value === null ? $value : json_encode($value), 'group' => $group, 'type' => $type],
        );
    }

    public function putMany(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            $type = match (true) {
                is_bool($value) => 'bool',
                is_int($value) => 'int',
                is_array($value) => 'json',
                default => 'string',
            };

            $this->put($key, $value, $group, $type);
        }
    }
}

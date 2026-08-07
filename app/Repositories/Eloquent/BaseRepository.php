<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository implements RepositoryInterface
{
    /** Relations eager loaded on every listing query. */
    protected array $with = [];

    /** Default ordering applied when a filter set does not specify one. */
    protected string $defaultSort = 'id';

    protected string $defaultDirection = 'desc';

    /** Columns `sort` may address, to keep the query string from reaching SQL. */
    protected array $sortable = ['id', 'created_at', 'updated_at'];

    abstract public function model(): string;

    public function query(): Builder
    {
        return ($this->model())::query()->with($this->with);
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->query()->get($columns);
    }

    /**
     * Filters are repository-specific; subclasses implement {@see applyFilters}.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applySorting($this->applyFilters($this->query(), $filters), $filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int|string $id): ?Model
    {
        return $this->query()->find($id);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function findBy(string $column, mixed $value): ?Model
    {
        return $this->query()->where($column, $value)->first();
    }

    public function create(array $attributes): Model
    {
        return ($this->model())::create($attributes);
    }

    public function update(Model $model, array $attributes): Model
    {
        $model->fill($attributes)->save();

        return $model;
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }

    public function count(array $filters = []): int
    {
        return $this->applyFilters(($this->model())::query(), $filters)->count();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query;
    }

    protected function applySorting(Builder $query, array $filters): Builder
    {
        $column = $filters['sort'] ?? $this->defaultSort;
        $direction = strtolower((string) ($filters['direction'] ?? $this->defaultDirection)) === 'asc' ? 'asc' : 'desc';

        if (! in_array($column, $this->sortable, true)) {
            $column = $this->defaultSort;
        }

        return $query->orderBy($column, $direction);
    }
}

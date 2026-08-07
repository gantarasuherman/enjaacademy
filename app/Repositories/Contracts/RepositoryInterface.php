<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    public function query(): Builder;

    public function all(array $columns = ['*']): Collection;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int|string $id): ?Model;

    public function findOrFail(int|string $id): Model;

    public function findBy(string $column, mixed $value): ?Model;

    public function create(array $attributes): Model;

    public function update(Model $model, array $attributes): Model;

    public function delete(Model $model): bool;

    public function count(array $filters = []): int;
}

<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface SettingRepositoryInterface extends RepositoryInterface
{
    /** key => typed value, for the whole table or one group. */
    public function pairs(?string $group = null): Collection;

    public function put(string $key, mixed $value, string $group = 'general', string $type = 'string'): void;

    public function putMany(array $values, string $group = 'general'): void;
}

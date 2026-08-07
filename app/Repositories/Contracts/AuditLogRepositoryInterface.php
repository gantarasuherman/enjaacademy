<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface AuditLogRepositoryInterface extends RepositoryInterface
{
    public function events(): Collection;

    public function auditableTypes(): Collection;

    public function latestFor(string $type, int|string $id, int $limit = 20): Collection;

    public function prune(int $days): int;
}

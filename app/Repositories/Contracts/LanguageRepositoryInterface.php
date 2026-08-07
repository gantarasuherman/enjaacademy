<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Language;
use Illuminate\Support\Collection;

interface LanguageRepositoryInterface extends RepositoryInterface
{
    public function active(): Collection;

    public function findBySlug(string $slug): ?Language;

    public function withModuleCounts(): Collection;
}

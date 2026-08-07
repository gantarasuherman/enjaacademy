<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function countByRole(): Collection;

    public function recentlyRegistered(int $limit = 5): Collection;

    public function leaderboard(int $limit = 10): Collection;

    public function registrationsPerMonth(int $months = 12): Collection;
}

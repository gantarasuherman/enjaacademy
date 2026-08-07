<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Collection;

interface QuizAttemptRepositoryInterface extends RepositoryInterface
{
    public function start(User $user, Quiz $quiz): QuizAttempt;

    public function recordAnswers(QuizAttempt $attempt, array $answers): void;

    public function historyFor(User $user, int $limit = 10): Collection;

    public function bestScore(User $user, Quiz $quiz): ?int;

    public function scoreTrend(User $user, int $limit = 10): Collection;
}

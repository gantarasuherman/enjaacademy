<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Quiz;
use Illuminate\Support\Collection;

interface QuizRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Quiz;

    public function publishedForModule(int $moduleId): Collection;

    /** Quiz with questions/options loaded and shuffled per its own settings. */
    public function loadForAttempt(Quiz $quiz): Quiz;

    public function forSelect(): Collection;
}

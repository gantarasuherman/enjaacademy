<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Repositories\Contracts\QuizAttemptRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class QuizAttemptRepository extends BaseRepository implements QuizAttemptRepositoryInterface
{
    protected array $with = ['quiz:id,title,slug'];

    protected string $defaultSort = 'created_at';

    protected array $sortable = ['id', 'score', 'created_at', 'finished_at'];

    public function model(): string
    {
        return QuizAttempt::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['user_id'] ?? null, fn (Builder $q, $id) => $q->where('user_id', $id))
            ->when($filters['quiz_id'] ?? null, fn (Builder $q, $id) => $q->where('quiz_id', $id))
            ->when(isset($filters['passed']) && $filters['passed'] !== '', fn (Builder $q) => $q->where('passed', (bool) $filters['passed']));
    }

    public function start(User $user, Quiz $quiz): QuizAttempt
    {
        return QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'total_questions' => $quiz->questions()->count(),
            'started_at' => now(),
        ]);
    }

    public function recordAnswers(QuizAttempt $attempt, array $answers): void
    {
        $now = now();

        QuizAnswer::query()->insert(collect($answers)->map(fn (array $answer) => [
            'quiz_attempt_id' => $attempt->id,
            'quiz_question_id' => $answer['question_id'],
            'quiz_option_id' => $answer['option_id'] ?? null,
            'answer_text' => $answer['answer_text'] ?? null,
            'is_correct' => $answer['is_correct'],
            'created_at' => $now,
            'updated_at' => $now,
        ])->all());
    }

    public function historyFor(User $user, int $limit = 10): Collection
    {
        return QuizAttempt::query()
            ->where('user_id', $user->id)
            ->finished()
            ->with('quiz:id,title,slug')
            ->latest('finished_at')
            ->limit($limit)
            ->get();
    }

    public function bestScore(User $user, Quiz $quiz): ?int
    {
        $best = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->finished()
            ->max('score');

        return $best === null ? null : (int) $best;
    }

    /** Chronological score series for the dashboard chart. */
    public function scoreTrend(User $user, int $limit = 10): Collection
    {
        return QuizAttempt::query()
            ->where('user_id', $user->id)
            ->finished()
            ->latest('finished_at')
            ->limit($limit)
            ->get(['id', 'score', 'finished_at'])
            ->reverse()
            ->values();
    }
}

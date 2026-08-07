<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Quiz;
use App\Repositories\Contracts\QuizRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class QuizRepository extends BaseRepository implements QuizRepositoryInterface
{
    protected array $with = ['module:id,name,slug,color'];

    protected string $defaultSort = 'created_at';

    protected array $sortable = ['id', 'title', 'difficulty', 'created_at'];

    public function model(): string
    {
        return Quiz::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->forModule($filters['module'] ?? null)
            ->when($filters['difficulty'] ?? null, fn (Builder $q, $d) => $q->where('difficulty', $d))
            ->when(isset($filters['is_published']) && $filters['is_published'] !== '', fn (Builder $q) => $q->where('is_published', (bool) $filters['is_published']))
            ->withCount('questions');
    }

    public function findBySlug(string $slug): ?Quiz
    {
        return Quiz::query()->with('module.language')->where('slug', $slug)->first();
    }

    public function publishedForModule(int $moduleId): Collection
    {
        return Quiz::query()
            ->published()
            ->where('learning_module_id', $moduleId)
            ->withCount('questions')
            ->orderBy('title')
            ->get();
    }

    /**
     * Shuffling happens in PHP after loading so the correct answer never has a
     * predictable position, while question ids stay stable for grading.
     */
    public function loadForAttempt(Quiz $quiz): Quiz
    {
        $quiz->load(['questions.options']);

        if ($quiz->shuffle_questions) {
            $quiz->setRelation('questions', $quiz->questions->shuffle()->values());
        }

        if ($quiz->shuffle_options) {
            $quiz->questions->each(
                fn ($question) => $question->setRelation('options', $question->options->shuffle()->values())
            );
        }

        return $quiz;
    }

    public function forSelect(): Collection
    {
        return Quiz::query()->orderBy('title')->get(['id', 'title', 'learning_module_id']);
    }
}

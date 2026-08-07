<?php

declare(strict_types=1);

namespace App\Services\Learning;

use App\DataTransferObjects\QuizData;
use App\DataTransferObjects\QuizSubmissionData;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Repositories\Contracts\QuizAttemptRepositoryInterface;
use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Services\Gamification\ProgressService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizService
{
    public function __construct(
        private readonly QuizRepositoryInterface $quizzes,
        private readonly QuizAttemptRepositoryInterface $attempts,
        private readonly ProgressService $progress,
    ) {}

    public function create(QuizData $data): Quiz
    {
        return DB::transaction(function () use ($data) {
            $attributes = $data->toArray();
            $attributes['created_by'] = auth()->id();

            /** @var Quiz $quiz */
            $quiz = $this->quizzes->create($attributes);

            $this->syncQuestions($quiz, $data->questions);

            return $quiz->refresh();
        });
    }

    public function update(Quiz $quiz, QuizData $data): Quiz
    {
        return DB::transaction(function () use ($quiz, $data) {
            $this->quizzes->update($quiz, $data->toArray());

            if ($data->questions !== []) {
                $this->syncQuestions($quiz, $data->questions);
            }

            return $quiz->refresh();
        });
    }

    public function delete(Quiz $quiz): void
    {
        $this->quizzes->delete($quiz);
    }

    /**
     * Replaces the question set wholesale. Simple and predictable for an
     * admin form that posts the entire question list every save.
     */
    private function syncQuestions(Quiz $quiz, array $questions): void
    {
        $keptIds = [];

        foreach (array_values($questions) as $index => $payload) {
            if (blank($payload['question'] ?? null)) {
                continue;
            }

            /** @var QuizQuestion $question */
            $question = QuizQuestion::updateOrCreate(
                ['id' => $payload['id'] ?? null, 'quiz_id' => $quiz->id],
                [
                    'question' => $payload['question'],
                    'type' => $payload['type'] ?? 'multiple_choice',
                    'explanation' => $payload['explanation'] ?? null,
                    'correct_text' => $payload['correct_text'] ?? null,
                    'score' => (int) ($payload['score'] ?? 1),
                    'sort_order' => $index,
                ],
            );

            $keptIds[] = $question->id;

            $question->options()->delete();

            foreach (array_values($payload['options'] ?? []) as $optionIndex => $option) {
                if (blank($option['label'] ?? null)) {
                    continue;
                }

                QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'label' => $option['label'],
                    'is_correct' => filter_var($option['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'sort_order' => $optionIndex,
                ]);
            }
        }

        $quiz->questions()->whereNotIn('id', $keptIds ?: [0])->delete();
    }

    public function start(User $user, Quiz $quiz): QuizAttempt
    {
        if (! $quiz->is_published) {
            throw ValidationException::withMessages(['quiz' => __('This quiz is not published yet.')]);
        }

        if ($quiz->questions()->count() === 0) {
            throw ValidationException::withMessages(['quiz' => __('This quiz has no questions yet.')]);
        }

        $left = $quiz->attemptsLeftFor($user);

        if ($left !== null && $left <= 0) {
            throw ValidationException::withMessages([
                'quiz' => __('You have used all :max attempts for this quiz.', ['max' => $quiz->max_attempts]),
            ]);
        }

        return $this->attempts->start($user, $quiz);
    }

    /**
     * Grades a submission server-side. The client never sees the answer key,
     * and a tampered payload simply scores zero on the tampered questions.
     */
    public function submit(User $user, Quiz $quiz, QuizSubmissionData $data): QuizAttempt
    {
        /** @var QuizAttempt $attempt */
        $attempt = QuizAttempt::query()
            ->where('id', $data->attemptId)
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->firstOrFail();

        if ($attempt->finished_at !== null) {
            throw ValidationException::withMessages(['attempt' => __('This attempt has already been submitted.')]);
        }

        return DB::transaction(function () use ($user, $quiz, $data, $attempt) {
            $questions = $quiz->questions()->with('options')->get()->keyBy('id');

            $graded = [];
            $correct = 0;

            foreach ($data->answers as $answer) {
                $question = $questions->get($answer['question_id']);

                if (! $question) {
                    continue;
                }

                $isCorrect = $question->isAnswerCorrect($answer['option_id'], $answer['answer_text']);
                $correct += $isCorrect ? 1 : 0;

                $graded[] = [
                    'question_id' => $question->id,
                    'option_id' => $answer['option_id'],
                    'answer_text' => $answer['answer_text'],
                    'is_correct' => $isCorrect,
                ];
            }

            $this->attempts->recordAnswers($attempt, $graded);

            $total = $questions->count();
            $score = $total > 0 ? (int) round($correct / $total * 100) : 0;

            $attempt->update([
                'total_questions' => $total,
                'correct_answers' => $correct,
                'score' => $score,
                'passed' => $score >= $quiz->pass_score,
                'finished_at' => now(),
                'duration_seconds' => $data->durationSeconds
                    ?? $attempt->started_at?->diffInSeconds(now()),
            ]);

            $this->progress->recordQuizAttempt($user, $attempt->refresh(), $quiz);

            return $attempt->refresh();
        });
    }

    /** Review payload: questions with the user's answer and the key. */
    public function review(QuizAttempt $attempt): array
    {
        $attempt->load(['answers', 'quiz.questions.options']);

        $answers = $attempt->answers->keyBy('quiz_question_id');

        return $attempt->quiz->questions->map(fn (QuizQuestion $question) => [
            'question' => $question,
            'answer' => $answers->get($question->id),
            'correct_option' => $question->correctOption(),
        ])->all();
    }
}

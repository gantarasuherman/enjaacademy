<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\QuizSubmissionData;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuizQuestionResource;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Repositories\Contracts\QuizAttemptRepositoryInterface;
use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Services\Learning\QuizService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function __construct(
        private readonly QuizRepositoryInterface $quizzes,
        private readonly QuizAttemptRepositoryInterface $attempts,
        private readonly QuizService $service,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $quizzes = Quiz::query()
            ->published()
            ->with('questions:id,quiz_id')
            ->forModule($request->query('moduleId'))
            ->forLesson($request->query('lessonId'))
            ->when($request->query('cefr'), fn (Builder $q, $level) => $q->where('level', $level))
            ->when($request->query('category'), fn (Builder $q, $category) => $q->where('category', $category))
            ->orderBy('title')
            ->get();

        return QuizResource::collection($quizzes);
    }

    public function show(Request $request, Quiz $quiz): QuizResource
    {
        $this->authorize('view', $quiz);

        return new QuizResource($quiz->load('questions:id,quiz_id'));
    }

    public function questions(Request $request, Quiz $quiz): AnonymousResourceCollection
    {
        $this->authorize('attempt', $quiz);

        $quiz = $this->quizzes->loadForAttempt($quiz);

        return QuizQuestionResource::collection($quiz->questions);
    }

    /**
     * Attempt history + stats + remaining-attempts info for one quiz, shown
     * before starting (so a repeat visitor sees their history first, not the
     * questions) and again on the result screen after finishing.
     */
    public function attempts(Request $request, Quiz $quiz): JsonResponse
    {
        $this->authorize('view', $quiz);

        $user = $request->user();
        $attempts = $this->attempts->forQuiz($user, $quiz);
        $scores = $attempts->pluck('score');
        $left = $quiz->attemptsLeftFor($user);

        $history = $attempts->values()
            ->map(fn (QuizAttempt $attempt, int $index) => [
                'attemptNumber' => $index + 1,
                'score' => $attempt->score,
                'correctCount' => $attempt->correct_answers,
                'totalCount' => $attempt->total_questions,
                'passed' => $attempt->passed,
                'durationSeconds' => $attempt->duration_seconds ?? 0,
                'completedAt' => $attempt->finished_at?->toIso8601String() ?? '',
            ])
            ->reverse()
            ->values();

        return response()->json(['data' => [
            'quizId' => (string) $quiz->id,
            'attemptsUsed' => $attempts->count(),
            'attemptsLimit' => $quiz->max_attempts,
            'attemptsLeft' => $left,
            'canAttempt' => $quiz->is_published && ($left === null || $left > 0),
            'best' => $scores->max(),
            'worst' => $scores->min(),
            'average' => $scores->isEmpty() ? null : (int) round($scores->avg()),
            'latest' => $attempts->last()?->score,
            'history' => $history,
        ]]);
    }

    public function submit(Request $request, Quiz $quiz): JsonResponse
    {
        $this->authorize('attempt', $quiz);

        $validated = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.questionId' => ['required'],
            'answers.*.given' => ['nullable'],
            'answers.*.timeSpentSeconds' => ['nullable', 'integer'],
            'durationSeconds' => ['nullable', 'integer'],
        ]);

        $user = $request->user();

        $attempt = DB::transaction(function () use ($user, $quiz, $validated) {
            $attempt = $this->service->start($user, $quiz);

            $questions = $quiz->questions()->with('options:id,quiz_question_id')->get()->keyBy('id');

            $answers = collect($validated['answers'])->map(function (array $record) use ($questions) {
                $questionId = (int) $record['questionId'];
                $question = $questions->get($questionId);
                $isFillBlank = $question?->type === 'fill_blank';
                // "arrange": the client sends the arranged word order as an
                // array (see frontend DragDrop/AnswerRecord.given) — joined
                // into one string so grading can reuse the fill_blank compare
                // path (QuizQuestion::isAnswerCorrect()).
                $isArrange = $question?->type === 'arrange';
                $given = $record['given'] ?? null;

                // A submitted option id must actually belong to this question, or the
                // FK on quiz_answers.quiz_option_id rejects the insert outright instead
                // of just scoring it wrong.
                $optionId = null;
                if (! $isFillBlank && ! $isArrange && is_numeric($given)) {
                    $candidate = (int) $given;
                    if ($question?->options->contains('id', $candidate)) {
                        $optionId = $candidate;
                    }
                }

                $answerText = match (true) {
                    $isFillBlank && is_string($given) => $given,
                    $isArrange && is_array($given) => implode(' ', array_map('strval', $given)),
                    default => null,
                };

                return [
                    'question_id' => $questionId,
                    'option_id' => $optionId,
                    'answer_text' => $answerText,
                ];
            })->values()->all();

            $data = new QuizSubmissionData(
                attemptId: $attempt->id,
                answers: $answers,
                durationSeconds: $validated['durationSeconds'] ?? null,
            );

            return $this->service->submit($user, $quiz, $data);
        });

        return response()->json(['data' => $this->toResultArray($attempt, $validated['answers'])]);
    }

    public function history(Request $request): JsonResponse
    {
        $attempts = $this->attempts->historyFor($request->user());

        return response()->json([
            'data' => $attempts->map(fn (QuizAttempt $attempt) => $this->toResultArray($attempt))->all(),
        ]);
    }

    /** @param array<int, array{questionId: mixed, given?: mixed, timeSpentSeconds?: mixed}> $submittedAnswers */
    private function toResultArray(QuizAttempt $attempt, array $submittedAnswers = []): array
    {
        $graded = $attempt->relationLoaded('answers')
            ? $attempt->answers->keyBy('quiz_question_id')
            : $attempt->answers()->get()->keyBy('quiz_question_id');

        $answers = collect($submittedAnswers)->map(function (array $record) use ($graded) {
            $questionId = (int) $record['questionId'];

            return [
                'questionId' => (string) $questionId,
                'given' => $record['given'] ?? '',
                'correct' => (bool) ($graded->get($questionId)?->is_correct ?? false),
                'timeSpentSeconds' => (int) ($record['timeSpentSeconds'] ?? 0),
            ];
        })->values()->all();

        return [
            'id' => (string) $attempt->id,
            'quizId' => (string) $attempt->quiz_id,
            'score' => $attempt->score,
            'correctCount' => $attempt->correct_answers,
            'totalCount' => $attempt->total_questions,
            'passed' => $attempt->passed,
            'earnedXp' => $attempt->earned_xp,
            'durationSeconds' => $attempt->duration_seconds ?? 0,
            'answers' => $answers,
            'completedAt' => $attempt->finished_at?->toIso8601String() ?? '',
        ];
    }
}

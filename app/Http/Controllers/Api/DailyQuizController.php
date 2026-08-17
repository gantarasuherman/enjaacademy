<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DailyQuizAttemptResource;
use App\Models\DailyQuizAttempt;
use App\Services\Vocabulary\DailyQuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyQuizController extends Controller
{
    public function __construct(private readonly DailyQuizService $service) {}

    /** Used by the app shell to decide whether to gate the user into the quiz before the dashboard. */
    public function status(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->service->status($request->user())]);
    }

    /** Fetches (or creates, if none exists yet for today) the current day's quiz — idempotent within the day. */
    public function show(Request $request): DailyQuizAttemptResource
    {
        $attempt = $this->service->getOrCreateTodayAttempt($request->user());

        return new DailyQuizAttemptResource($attempt);
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'attemptId' => ['required', 'integer'],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.questionId' => ['required', 'integer'],
            'answers.*.answer' => ['nullable', 'string'],
        ]);

        $attempt = DailyQuizAttempt::where('id', $validated['attemptId'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $result = $this->service->submit($request->user(), $attempt, $validated['answers']);

        return response()->json(['data' => $this->toReviewArray($result)]);
    }

    public function skip(Request $request): JsonResponse
    {
        $attempt = $this->service->skip($request->user());

        return response()->json(['data' => new DailyQuizAttemptResource($attempt)]);
    }

    private function toReviewArray(DailyQuizAttempt $attempt): array
    {
        return [
            'id' => (string) $attempt->id,
            'score' => $attempt->score,
            'correctCount' => $attempt->correct_count,
            'totalQuestions' => $attempt->total_questions,
            'earnedXp' => $attempt->earned_xp,
            'completedAt' => $attempt->completed_at?->toIso8601String(),
            'review' => $attempt->questions->map(fn ($q) => [
                'questionId' => (string) $q->id,
                'word' => $q->word?->word,
                'type' => $q->type,
                'prompt' => $q->payload['prompt'] ?? '',
                'givenAnswer' => $q->given_answer,
                'correctAnswer' => $q->payload['answer'] ?? $q->payload['correctAnswer'] ?? null,
                'isCorrect' => (bool) $q->is_correct,
            ])->values(),
        ];
    }
}

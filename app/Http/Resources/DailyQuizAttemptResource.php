<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyQuizAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'quizDate' => $this->quiz_date->toDateString(),
            'totalQuestions' => $this->total_questions,
            'correctCount' => $this->correct_count,
            'score' => $this->score,
            'skipped' => $this->skipped,
            'completedAt' => $this->completed_at?->toIso8601String(),
            'questions' => DailyQuizQuestionResource::collection($this->whenLoaded('questions')),
        ];
    }
}

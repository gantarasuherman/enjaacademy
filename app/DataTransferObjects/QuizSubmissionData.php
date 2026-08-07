<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;

class QuizSubmissionData extends BaseData
{
    public function __construct(
        public readonly int $attemptId,
        /** @var array<int, array{question_id:int, option_id:?int, answer_text:?string}> */
        public readonly array $answers,
        public readonly ?int $durationSeconds,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $answers = collect((array) $request->input('answers', []))
            ->map(fn ($answer, $questionId) => [
                'question_id' => (int) $questionId,
                'option_id' => is_numeric($answer['option_id'] ?? $answer) ? (int) ($answer['option_id'] ?? $answer) : null,
                'answer_text' => is_array($answer) ? ($answer['answer_text'] ?? null) : null,
            ])
            ->values()
            ->all();

        return new static(
            attemptId: (int) $request->input('attempt_id'),
            answers: $answers,
            durationSeconds: $request->filled('duration_seconds') ? (int) $request->input('duration_seconds') : null,
        );
    }

    public function toArray(): array
    {
        return [
            'attempt_id' => $this->attemptId,
            'answers' => $this->answers,
            'duration_seconds' => $this->durationSeconds,
        ];
    }
}

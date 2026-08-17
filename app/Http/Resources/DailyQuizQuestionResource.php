<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Question view used while the quiz is in progress — `payload` is stripped
 * of `correctAnswer`/`answer` so the client never receives the answer key.
 * The full payload (with the key) is only ever read server-side for grading.
 */
class DailyQuizQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $payload = collect($this->payload)->except(['correctAnswer', 'answer'])->all();

        return [
            'id' => (string) $this->id,
            'type' => $this->type,
            'payload' => $payload,
        ];
    }
}

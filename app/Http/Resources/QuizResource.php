<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'lessonId' => $this->lesson_id ? (string) $this->lesson_id : null,
            'moduleId' => $this->learning_module_id ? (string) $this->learning_module_id : null,
            'title' => $this->title,
            'description' => $this->description ?? '',
            'cefr' => $this->level,
            'timeLimitSeconds' => $this->time_limit_seconds,
            'passScore' => $this->pass_score,
            'xpReward' => $this->xp_reward,
            'questionIds' => $this->whenLoaded(
                'questions',
                fn () => $this->questions->map(fn (QuizQuestion $q) => (string) $q->id)->all(),
                [],
            ),
        ];
    }
}

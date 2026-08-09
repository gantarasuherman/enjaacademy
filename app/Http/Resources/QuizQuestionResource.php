<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\QuizOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class QuizQuestionResource extends JsonResource
{
    private const TYPE_MAP = [
        'multiple_choice' => 'multiple-choice',
        'true_false' => 'true-false',
        'fill_blank' => 'fill-blank',
        'matching' => 'matching',
    ];

    public function toArray(Request $request): array
    {
        $isFillBlank = $this->type === 'fill_blank';
        $correctOption = $isFillBlank ? null : $this->options->firstWhere('is_correct', true);

        return [
            'id' => (string) $this->id,
            'type' => self::TYPE_MAP[$this->type] ?? 'multiple-choice',
            'prompt' => $this->question,
            'audioUrl' => $this->audio_path ? Storage::disk('public')->url($this->audio_path) : null,
            'imageUrl' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'options' => $this->when(! $isFillBlank, fn () => $this->options->map(fn (QuizOption $option) => [
                'id' => (string) $option->id,
                'label' => $option->label,
                'imageUrl' => $option->image_path ? Storage::disk('public')->url($option->image_path) : null,
            ])->all()),
            'correctAnswer' => $isFillBlank ? (string) $this->correct_text : (string) ($correctOption?->id ?? ''),
            'explanation' => $this->explanation ?? '',
            'points' => $this->score,
        ];
    }
}

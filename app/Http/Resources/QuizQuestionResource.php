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
        'arrange' => 'drag-drop',
    ];

    public function toArray(Request $request): array
    {
        $isFillBlank = $this->type === 'fill_blank';
        $isArrange = $this->type === 'arrange';
        $correctOption = (! $isFillBlank && ! $isArrange) ? $this->options->firstWhere('is_correct', true) : null;

        // "arrange": word bank is `quiz_options` (one row per word), already
        // ordered by `sort_order` via the `options()` relation — that order
        // IS the correct sentence. The frontend shuffles `tokens` itself
        // (QuestionRenderer's DragDrop component) and compares the student's
        // arrangement against `correctAnswer`.
        $words = $isArrange ? $this->options->pluck('label')->values() : null;

        return [
            'id' => (string) $this->id,
            'type' => self::TYPE_MAP[$this->type] ?? 'multiple-choice',
            'prompt' => $this->question,
            'audioUrl' => $this->audio_path ? Storage::disk('public')->url($this->audio_path) : null,
            'imageUrl' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'options' => $this->when(! $isFillBlank && ! $isArrange, fn () => $this->options->map(fn (QuizOption $option) => [
                'id' => (string) $option->id,
                'label' => $option->label,
                'imageUrl' => $option->image_path ? Storage::disk('public')->url($option->image_path) : null,
            ])->all()),
            'tokens' => $this->when($isArrange, fn () => $words->all()),
            'correctAnswer' => match (true) {
                $isFillBlank => (string) $this->correct_text,
                $isArrange => $words->all(),
                default => (string) ($correctOption?->id ?? ''),
            },
            'explanation' => $this->explanation ?? '',
            'points' => $this->score,
        ];
    }
}

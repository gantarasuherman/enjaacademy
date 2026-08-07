<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'question', 'type', 'audio_path', 'image_path',
        'explanation', 'correct_text', 'score', 'sort_order',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class)->orderBy('sort_order');
    }

    public function correctOption(): ?QuizOption
    {
        return $this->options->firstWhere('is_correct', true);
    }

    /** Grade a submitted answer without leaking the key to the caller. */
    public function isAnswerCorrect(?int $optionId, ?string $text): bool
    {
        return match ($this->type) {
            'fill_blank' => filled($text)
                && mb_strtolower(trim($text)) === mb_strtolower(trim((string) $this->correct_text)),
            default => $optionId !== null
                && $this->options->firstWhere('id', $optionId)?->is_correct === true,
        };
    }
}

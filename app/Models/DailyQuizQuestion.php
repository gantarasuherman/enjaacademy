<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyQuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_quiz_attempt_id', 'vocabulary_word_id', 'type', 'payload',
        'given_answer', 'is_correct', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'is_correct' => 'boolean',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(DailyQuizAttempt::class, 'daily_quiz_attempt_id');
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(VocabularyWord::class, 'vocabulary_word_id');
    }
}

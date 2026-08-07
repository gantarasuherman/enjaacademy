<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id', 'quiz_id', 'total_questions', 'correct_answers', 'score',
        'earned_xp', 'passed', 'started_at', 'finished_at', 'duration_seconds',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function scopeFinished(Builder $query): Builder
    {
        return $query->whereNotNull('finished_at');
    }

    public function isPerfect(): bool
    {
        return $this->total_questions > 0 && $this->correct_answers === $this->total_questions;
    }
}

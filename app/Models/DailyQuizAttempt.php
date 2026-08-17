<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyQuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'quiz_date', 'total_questions', 'correct_count', 'score', 'earned_xp', 'skipped', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'quiz_date' => 'date',
            'skipped' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DailyQuizQuestion::class)->orderBy('sort_order');
    }

    public function isFinished(): bool
    {
        return $this->skipped || $this->completed_at !== null;
    }
}

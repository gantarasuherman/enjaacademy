<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashcardReview extends Model
{
    protected $fillable = [
        'user_id', 'flashcard_id', 'ease_factor', 'interval_days', 'repetitions',
        'lapses', 'last_quality', 'last_reviewed_at', 'due_at',
    ];

    protected function casts(): array
    {
        return [
            'ease_factor' => 'decimal:2',
            'last_reviewed_at' => 'datetime',
            'due_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q->whereNull('due_at')->orWhere('due_at', '<=', now()));
    }
}

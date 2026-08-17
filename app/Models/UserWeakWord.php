<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWeakWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'vocabulary_word_id', 'wrong_count', 'correct_streak', 'last_wrong_at', 'mastered',
    ];

    protected function casts(): array
    {
        return [
            'last_wrong_at' => 'datetime',
            'mastered' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(VocabularyWord::class, 'vocabulary_word_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('mastered', false);
    }
}

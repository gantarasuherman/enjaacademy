<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * A single learnable atom: one kana, one kanji, one vocabulary entry, one
 * grammar point or one conversation line. `extra` holds type-specific fields.
 */
class LessonItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id', 'term', 'reading', 'romaji', 'meaning', 'example',
        'example_meaning', 'audio_path', 'image_path', 'extra', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('term', 'like', "%{$term}%")
                ->orWhere('reading', 'like', "%{$term}%")
                ->orWhere('romaji', 'like', "%{$term}%")
                ->orWhere('meaning', 'like', "%{$term}%");
        }));
    }

    public function extraValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->extra, $key, $default);
    }
}

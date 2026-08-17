<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VocabularyWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id', 'word', 'phonetic', 'part_of_speech',
        'meaning_id', 'meaning_en', 'level', 'synonyms', 'antonyms', 'collocations',
    ];

    protected function casts(): array
    {
        return [
            'synonyms' => 'array',
            'antonyms' => 'array',
            'collocations' => 'array',
        ];
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function examples(): HasMany
    {
        return $this->hasMany(VocabularyWordExample::class)->orderBy('sort_order');
    }

    public function scopeForLevel(Builder $query, ?string $level): Builder
    {
        return $query->when($level, fn (Builder $q) => $q->where('level', $level));
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q
            ->where('word', 'like', "%{$term}%")
            ->orWhere('meaning_id', 'like', "%{$term}%")
            ->orWhere('meaning_en', 'like', "%{$term}%"));
    }
}

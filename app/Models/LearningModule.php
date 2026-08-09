<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Hiragana, Katakana, Kanji, TOEFL, IELTS ... are rows in this table, not
 * classes. New modules are created from the admin panel.
 */
class LearningModule extends Model
{
    use Auditable;
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected string $slugSource = 'name';

    protected $fillable = [
        'language_id', 'name', 'slug', 'icon', 'color', 'content_type',
        'permission_prefix', 'description', 'sort_order', 'is_active', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function publishedLessons(): HasMany
    {
        return $this->lessons()->where('is_published', true);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function flashcardDecks(): HasMany
    {
        return $this->hasMany(FlashcardDeck::class);
    }

    public function progress(): MorphMany
    {
        return $this->morphMany(UserProgress::class, 'trackable');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForLanguage(Builder $query, int|string|null $language): Builder
    {
        return $query->when($language, function (Builder $q) use ($language) {
            is_numeric($language)
                ? $q->where('language_id', $language)
                : $q->whereHas('language', fn ($l) => $l->where('slug', $language));
        });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")->orWhere('slug', 'like', "%{$term}%");
        }));
    }

    /**
     * `{permission_prefix}.{action}`, falling back to the slug so a module
     * created in the panel is gated even if the prefix field was left blank.
     */
    public function permission(string $action = 'view'): string
    {
        return ($this->permission_prefix ?: str_replace('-', '_', $this->slug)).'.'.$action;
    }
}

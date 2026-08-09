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

class Lesson extends Model
{
    use Auditable;
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected string $slugSource = 'title';

    protected $fillable = [
        'learning_module_id', 'title', 'slug', 'level', 'summary', 'content',
        'translated_content', 'cover_image', 'audio_path', 'video_url', 'estimated_minutes',
        'xp_reward', 'sort_order', 'is_published', 'published_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(LearningModule::class, 'learning_module_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LessonItem::class)->orderBy('sort_order');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function progress(): MorphMany
    {
        return $this->morphMany(UserProgress::class, 'trackable');
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeLevel(Builder $query, ?string $level): Builder
    {
        return $query->when($level, fn (Builder $q) => $q->where('level', $level));
    }

    public function scopeForModule(Builder $query, int|string|null $module): Builder
    {
        return $query->when($module, function (Builder $q) use ($module) {
            is_numeric($module)
                ? $q->where('learning_module_id', $module)
                : $q->whereHas('module', fn ($m) => $m->where('slug', $module));
        });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")->orWhere('summary', 'like', "%{$term}%");
        }));
    }
}

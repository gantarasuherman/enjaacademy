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
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use Auditable;
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected string $slugSource = 'title';

    protected $fillable = [
        'learning_module_id', 'lesson_id', 'title', 'slug', 'description', 'level',
        'difficulty', 'time_limit_seconds', 'pass_score', 'xp_reward', 'max_attempts',
        'shuffle_questions', 'shuffle_options', 'show_explanation', 'is_published', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_explanation' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(LearningModule::class, 'learning_module_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
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
        return $query->when($term, fn (Builder $q) => $q->where('title', 'like', "%{$term}%"));
    }

    public function totalScore(): int
    {
        return (int) $this->questions()->sum('score');
    }

    /**
     * The API resource exposes the numeric id (not the slug) as `id`, since
     * that also has to match `QuizResult.quizId`. Accept either here so
     * route-model binding works for both the admin (slug) and API (id) callers.
     * The OR is nested so it stays scoped correctly under the SoftDeletes
     * global scope's `whereNull(deleted_at)` instead of escaping it.
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        $key = $field ?? $this->getRouteKeyName();

        return $this->newQuery()
            ->where(function (Builder $q) use ($key, $value) {
                $q->where($key, $value);

                if (is_numeric($value)) {
                    $q->orWhere($this->getKeyName(), $value);
                }
            })
            ->first();
    }

    public function attemptsLeftFor(User $user): ?int
    {
        if (! $this->max_attempts) {
            return null;
        }

        return max(0, $this->max_attempts - $this->attempts()->where('user_id', $user->id)->count());
    }
}

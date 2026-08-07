<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    use Auditable;
    use HasFactory;
    use HasSlug;

    protected string $slugSource = 'name';

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'badge_color',
        'criteria_type', 'criteria_value', 'xp_reward', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['progress', 'unlocked_at'])
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where('name', 'like', "%{$term}%"));
    }

    /** The UserStat column this achievement is measured against. */
    public function statColumn(): ?string
    {
        return match ($this->criteria_type) {
            'xp_total' => 'xp_total',
            'level' => 'level',
            'lessons_completed' => 'lessons_completed',
            'quizzes_completed' => 'quizzes_completed',
            'perfect_quizzes' => 'perfect_quizzes',
            'streak_days' => 'streak_days',
            'flashcards_reviewed' => 'flashcards_reviewed',
            default => null,
        };
    }
}

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

class FlashcardDeck extends Model
{
    use Auditable;
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected string $slugSource = 'name';

    protected $fillable = [
        'user_id', 'learning_module_id', 'name', 'slug', 'description',
        'color', 'icon', 'is_public', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(LearningModule::class, 'learning_module_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Flashcard::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Decks the user owns plus every published system/public deck. */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        return $query->where(function (Builder $q) use ($user) {
            $q->where('is_public', true)->orWhereNull('user_id');

            if ($user) {
                $q->orWhere('user_id', $user->id);
            }
        });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where('name', 'like', "%{$term}%"));
    }

    public function isSystemDeck(): bool
    {
        return $this->user_id === null;
    }
}

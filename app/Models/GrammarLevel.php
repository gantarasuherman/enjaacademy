<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * N5..N1 out of the box, but a row — not an enum — so an admin can add a new
 * level (or a non-JLPT scheme entirely) without a deploy. `language` +
 * `track` split the table into independent trees: (japanese, grammar),
 * (english, grammar), (japanese, structure), (english, structure) today,
 * with room for more of either axis later without a schema change.
 */
class GrammarLevel extends Model
{
    use HasFactory;
    use HasSlug;

    protected string $slugSource = 'name';

    protected $fillable = ['name', 'language', 'track', 'slug', 'color', 'description', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(GrammarCategory::class);
    }

    public function scopeForTrack(Builder $query, string $language, string $track): Builder
    {
        return $query->where('language', $language)->where('track', $track);
    }

    /** Slug uniqueness is scoped to this level's (language, track) tree, not global. */
    public function generateUniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $i = 2;

        while (static::withoutGlobalScopes()
            ->where('slug', $slug)
            ->where('language', $this->language)
            ->where('track', $this->track)
            ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
            ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

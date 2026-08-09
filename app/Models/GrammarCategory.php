<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Self-nesting: a category with `parent_id` set is what the UI calls a
 * "subcategory" — one table covers both, so an admin can go arbitrarily deep
 * without a schema change.
 */
class GrammarCategory extends Model
{
    use HasFactory;

    protected $fillable = ['grammar_level_id', 'parent_id', 'name', 'slug', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(GrammarLevel::class, 'grammar_level_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function patterns(): HasMany
    {
        return $this->hasMany(GrammarPattern::class);
    }
}

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

class GrammarPattern extends Model
{
    use Auditable;
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected string $slugSource = 'title';

    protected $fillable = [
        'grammar_category_id', 'title', 'slug', 'explanation', 'formula',
        'sort_order', 'is_published', 'published_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(GrammarCategory::class, 'grammar_category_id');
    }

    public function examples(): HasMany
    {
        return $this->hasMany(GrammarPatternExample::class)->where('type', 'example')->orderBy('sort_order');
    }

    public function mistakes(): HasMany
    {
        return $this->hasMany(GrammarPatternExample::class)->where('type', 'mistake')->orderBy('sort_order');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GrammarPatternExample::class)->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'notable_type', 'notable_id', 'title', 'body', 'color', 'is_pinned',
    ];

    protected function casts(): array
    {
        return ['is_pinned' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePinnedFirst(Builder $query): Builder
    {
        return $query->orderByDesc('is_pinned')->orderByDesc('updated_at');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")->orWhere('body', 'like', "%{$term}%");
        }));
    }
}

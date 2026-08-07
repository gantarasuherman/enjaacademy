<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'event', 'auditable_type', 'auditable_id',
        'auditable_label', 'old_values', 'new_values', 'url', 'method',
        'ip_address', 'user_agent', 'description',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeEvent(Builder $query, ?string $event): Builder
    {
        return $query->when($event, fn (Builder $q) => $q->where('event', $event));
    }

    public function scopeForUser(Builder $query, int|string|null $userId): Builder
    {
        return $query->when($userId, fn (Builder $q) => $q->where('user_id', $userId));
    }

    public function scopeForType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $q) => $q->where('auditable_type', $type));
    }

    public function scopeBetween(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn (Builder $q) => $q->whereDate('created_at', '<=', $to));
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('user_name', 'like', "%{$term}%")
                ->orWhere('auditable_label', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        }));
    }

    /** Attribute-level diff between old and new values. */
    public function changes(): array
    {
        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];

        return collect(array_keys($old + $new))
            ->mapWithKeys(fn (string $key) => [$key => [
                'old' => $old[$key] ?? null,
                'new' => $new[$key] ?? null,
            ]])
            ->reject(fn (array $pair) => $pair['old'] === $pair['new'])
            ->all();
    }
}

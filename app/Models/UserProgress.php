<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserProgress extends Model
{
    protected $table = 'user_progress';

    protected $fillable = [
        'user_id', 'trackable_type', 'trackable_id', 'status', 'progress_percent',
        'time_spent_seconds', 'started_at', 'completed_at', 'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeOfType(Builder $query, string $morphAlias): Builder
    {
        return $query->where('trackable_type', $morphAlias);
    }
}

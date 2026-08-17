<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Awarded automatically once a student completes 100% of a module's published lessons — see ProgressService::maybeIssueCertificate(). */
class Certificate extends Model
{
    protected $fillable = ['user_id', 'learning_module_id', 'issued_at'];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function learningModule(): BelongsTo
    {
        return $this->belongsTo(LearningModule::class);
    }
}

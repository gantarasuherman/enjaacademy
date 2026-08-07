<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Opt-in marker + relation for models tracked by {@see \App\Observers\AuditableObserver}.
 */
trait Auditable
{
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /** Attributes never written into the audit payload. */
    public function auditHidden(): array
    {
        return array_merge(
            config('admin.audit.hidden_attributes', []),
            property_exists($this, 'auditHidden') ? $this->auditHidden : [],
        );
    }

    /** Human label shown in the audit log listing. */
    public function auditLabel(): string
    {
        foreach (['title', 'name', 'email', 'slug'] as $candidate) {
            if (! empty($this->{$candidate})) {
                return (string) $this->{$candidate};
            }
        }

        return class_basename($this).'#'.$this->getKey();
    }
}

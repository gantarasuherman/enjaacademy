<?php

declare(strict_types=1);

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Writes one audit row. Never throws: a broken audit trail must not take
     * down the write that triggered it.
     */
    public function record(
        string $event,
        ?Model $subject = null,
        ?array $old = null,
        ?array $new = null,
        ?string $description = null,
    ): ?AuditLog {
        if (! config('admin.audit.enabled', true)) {
            return null;
        }

        try {
            $user = auth()->user();
            $request = request();

            return AuditLog::create([
                'user_id' => $user?->getKey(),
                'user_name' => $user?->name,
                'event' => $event,
                'auditable_type' => $subject?->getMorphClass(),
                'auditable_id' => $subject?->getKey(),
                'auditable_label' => $this->labelFor($subject),
                'old_values' => $this->scrub($old, $subject),
                'new_values' => $this->scrub($new, $subject),
                'url' => $request?->fullUrl(),
                'method' => $request?->method(),
                'ip_address' => $request?->ip(),
                'user_agent' => substr((string) $request?->userAgent(), 0, 500),
                'description' => $description,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log', [
                'event' => $event,
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /** Convenience for non-model events (login, backup, import, ...). */
    public function event(string $event, string $description, ?Model $subject = null): ?AuditLog
    {
        return $this->record($event, $subject, null, null, $description);
    }

    private function labelFor(?Model $subject): ?string
    {
        if (! $subject) {
            return null;
        }

        return method_exists($subject, 'auditLabel')
            ? $subject->auditLabel()
            : class_basename($subject).'#'.$subject->getKey();
    }

    /** Strips passwords, tokens and anything else the model marks hidden. */
    private function scrub(?array $values, ?Model $subject): ?array
    {
        if ($values === null) {
            return null;
        }

        $hidden = $subject && method_exists($subject, 'auditHidden')
            ? $subject->auditHidden()
            : config('admin.audit.hidden_attributes', []);

        return collect($values)
            ->except($hidden)
            ->map(fn ($value) => $value instanceof \DateTimeInterface ? $value->format('c') : $value)
            ->all();
    }
}

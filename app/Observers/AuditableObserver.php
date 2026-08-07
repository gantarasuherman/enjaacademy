<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * Records created / updated / deleted / restored events for models that use
 * the {@see \App\Models\Concerns\Auditable} trait.
 */
class AuditableObserver
{
    public function __construct(private readonly AuditLogger $logger) {}

    public function created(Model $model): void
    {
        $this->logger->record('created', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changed = $model->getChanges();

        unset($changed['updated_at']);

        if ($changed === []) {
            return;
        }

        $old = array_intersect_key($model->getOriginal(), $changed);

        $this->logger->record('updated', $model, $old, $changed);
    }

    public function deleted(Model $model): void
    {
        $event = method_exists($model, 'isForceDeleting') && $model->isForceDeleting()
            ? 'force_deleted'
            : 'deleted';

        $this->logger->record($event, $model, $model->getOriginal(), null);
    }

    public function restored(Model $model): void
    {
        $this->logger->record('restored', $model, null, $model->getAttributes());
    }
}

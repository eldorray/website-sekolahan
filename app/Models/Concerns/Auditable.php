<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Records a trail of create / update / delete / restore events for a model
 * into the audit_logs table, capturing who did it and what changed.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn (Model $m) => $m->writeAudit('created', $m->getAttributes()));

        static::updated(fn (Model $m) => $m->writeAudit('updated', $m->getChanges()));

        static::deleted(function (Model $m) {
            $event = method_exists($m, 'isForceDeleting') && $m->isForceDeleting()
                ? 'force_deleted'
                : 'deleted';
            $m->writeAudit($event, null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(fn (Model $m) => $m->writeAudit('restored', null));
        }
    }

    public function audits()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest('created_at');
    }

    protected function writeAudit(string $event, ?array $changes): void
    {
        // Don't record the audit_log's own timestamps churn.
        if (is_array($changes)) {
            unset($changes['updated_at'], $changes['created_at']);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => $this->getMorphClass(),
            'auditable_id' => $this->getKey(),
            'changes' => $changes ?: null,
            'created_at' => now(),
        ]);
    }
}

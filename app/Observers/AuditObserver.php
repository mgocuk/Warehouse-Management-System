<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->logAction($model, 'CREATE', null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $oldData = array_intersect_key($model->getOriginal(), $model->getChanges());
        $newData = $model->getChanges();

        // Skip timestamps-only and internal token-only updates
        unset(
            $oldData['updated_at'], $newData['updated_at'],
            $oldData['remember_token'], $newData['remember_token']
        );

        if (!empty($newData)) {
            $this->logAction($model, 'UPDATE', $oldData, $newData);
        }
    }

    public function deleted(Model $model): void
    {
        // Distinguish soft-delete from hard-delete
        $action = method_exists($model, 'bootSoftDeletes') && !$model->isForceDeleting()
            ? 'SOFT_DELETE'
            : 'DELETE';

        $this->logAction($model, $action, $model->getAttributes(), null);
    }

    /**
     * Called when a soft-deleted model is restored.
     */
    public function restored(Model $model): void
    {
        $this->logAction($model, 'RESTORE', null, $model->getAttributes());
    }

    /**
     * Called when a model is permanently deleted (forceDelete).
     */
    public function forceDeleted(Model $model): void
    {
        $this->logAction($model, 'FORCE_DELETE', $model->getAttributes(), null);
    }

    protected function logAction(Model $model, string $action, ?array $oldData, ?array $newData): void
    {
        try {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => $action,
                'table_name' => $model->getTable(),
                'record_id'  => $model->getKey(),
                'old_data'   => $oldData,
                'new_data'   => $newData,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Silence to not break primary operations if logging fails
        }
    }
}

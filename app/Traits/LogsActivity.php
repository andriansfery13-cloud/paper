<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    /**
     * Log activity for this model
     */
    public function logActivity($action, $oldValues = null, $newValues = null)
    {
        if (!auth()->check()) {
            return;
        }

        ActivityLog::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $this->getActivityLogModule(),
            'subject_type' => get_class($this),
            'subject_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $this->getActivityDescription($action),
        ]);
    }

    /**
     * Get the module name for activity log
     */
    protected function getActivityLogModule()
    {
        return strtolower(class_basename($this));
    }

    /**
     * Get activity description
     */
    protected function getActivityDescription($action)
    {
        $modelName = class_basename($this);
        $identifier = $this->getActivityIdentifier();

        return "{$action} {$modelName}: {$identifier}";
    }

    /**
     * Get identifier for activity log
     */
    protected function getActivityIdentifier()
    {
        return $this->name ?? $this->title ?? $this->id;
    }
}

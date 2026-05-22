<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * Boot the trait - add global scope to filter by tenant
     */
    protected static function bootBelongsToTenant()
    {
        // Add global scope to filter data by current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', auth()->user()->tenant_id);
            }
        });

        // Auto-set tenant_id when creating
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id && !$model->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /**
     * Get the tenant that owns this model
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    /**
     * Scope to filter by specific tenant
     */
    public function scopeForTenant(Builder $query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to bypass tenant filter (for admin purposes)
     */
    public function scopeWithoutTenantScope(Builder $query)
    {
        return $query->withoutGlobalScope('tenant');
    }
}

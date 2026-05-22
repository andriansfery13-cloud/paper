<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class DocumentTemplate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'html_content',
        'settings',
        'is_default',
        'is_system',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public static function getDefaultTemplate($type, $tenantId = null)
    {
        // First try to find tenant's default
        if ($tenantId) {
            $template = static::where('tenant_id', $tenantId)
                ->where('type', $type)
                ->where('is_default', true)
                ->first();

            if ($template) {
                return $template;
            }
        }

        // Fall back to system template
        return static::whereNull('tenant_id')
            ->where('type', $type)
            ->where('is_system', true)
            ->first();
    }
}

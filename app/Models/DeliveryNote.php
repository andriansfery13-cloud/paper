<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
use App\Traits\HasVerificationCode;

class DeliveryNote extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, HasVerificationCode;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'created_by',
        'delivery_number',
        'delivery_date',
        'recipient_name',
        'recipient_address',
        'recipient_phone',
        'driver_name',
        'vehicle_number',
        'notes',
        'status',
        'delivered_at',
        'received_by_name',
        'receiver_signature',
        'document_hash',
        'verification_code',
        'signature_image',
        'include_signature',
        'include_stamp',
        'include_qr',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivered_at' => 'datetime',
        'include_signature' => 'boolean',
        'include_stamp' => 'boolean',
        'include_qr' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($note) {
            if (!$note->delivery_number) {
                $note->delivery_number = static::generateNumber($note->tenant_id);
            }
        });
    }

    public static function generateNumber($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        $prefix = $tenant ? $tenant->delivery_prefix : 'DO';
        $year = date('Y');
        $month = date('m');

        $lastNote = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastNote ? (int) substr($lastNote->delivery_number, -5) + 1 : 1;
        return "{$prefix}/{$year}{$month}/" . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(DeliveryNoteItem::class)->orderBy('sort_order');
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'in_transit' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu',
            'in_transit' => 'Dalam Perjalanan',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    // Actions
    public function markAsInTransit()
    {
        $this->update(['status' => 'in_transit']);
    }

    public function markAsDelivered($receivedBy = null)
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'received_by_name' => $receivedBy,
        ]);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }
}

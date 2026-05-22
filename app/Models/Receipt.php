<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
use App\Traits\HasVerificationCode;

class Receipt extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, HasVerificationCode;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'payment_id',
        'created_by',
        'receipt_number',
        'receipt_date',
        'amount',
        'notes',
        'document_hash',
        'verification_code',
        'signature_image',
        'stamp_image',
        'include_signature',
        'include_stamp',
        'include_qr',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
        'include_signature' => 'boolean',
        'include_stamp' => 'boolean',
        'include_qr' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            if (!$receipt->receipt_number) {
                $receipt->receipt_number = static::generateNumber($receipt->tenant_id);
            }
        });
    }

    public static function generateNumber($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        $prefix = $tenant ? $tenant->receipt_prefix : 'REC';
        $year = date('Y');
        $month = date('m');

        $lastReceipt = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastReceipt ? (int) substr($lastReceipt->receipt_number, -5) + 1 : 1;
        return "{$prefix}/{$year}{$month}/" . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}

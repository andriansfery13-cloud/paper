<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use App\Traits\HasVerificationCode;

class Quotation extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, LogsActivity, HasVerificationCode;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'created_by',
        'quotation_number',
        'quotation_date',
        'valid_until',
        'subject',
        'notes',
        'terms',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_amount',
        'total',
        'status',
        'document_hash',
        'verification_code',
        'signature_image',
        'stamp_image',
        'sent_at',
        'approved_at',
        'rejected_at',
        'approved_by',
        'rejection_reason',
        'include_signature',
        'include_stamp',
        'include_qr',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'sent_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'include_signature' => 'boolean',
        'include_stamp' => 'boolean',
        'include_qr' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            if (!$quotation->quotation_number) {
                $quotation->quotation_number = static::generateNumber($quotation->tenant_id);
            }
        });
    }

    public static function generateNumber($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        $prefix = $tenant ? $tenant->quotation_prefix : 'QUO';
        $year = date('Y');
        $month = date('m');

        $lastQuotation = static::withoutGlobalScope('tenant')
            ->withTrashed() // Include soft-deleted records to prevent duplicates
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastQuotation ? (int) substr($lastQuotation->quotation_number, -5) + 1 : 1;
        return "{$prefix}/{$year}{$month}/" . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Status Methods
    public function isExpired()
    {
        return $this->valid_until < now() && $this->status !== 'approved';
    }

    public function canBeConverted()
    {
        return $this->status === 'approved' && !$this->isExpired();
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft']);
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getVerificationUrlAttribute()
    {
        return route('verify.quotation', $this->verification_code);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'sent' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'expired' => 'warning',
            'converted' => 'primary',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    // Calculation Methods
    public function calculateTotals()
    {
        $subtotal = $this->items->sum('subtotal');
        $taxAmount = $this->items->sum('tax_amount');

        $discountAmount = 0;
        if ($this->discount_type == 1) {
            $discountAmount = $subtotal * ($this->discount_value / 100);
        } else {
            $discountAmount = $this->discount_value ?? 0;
        }

        $this->subtotal = $subtotal;
        $this->tax_amount = $taxAmount;
        $this->discount_amount = $discountAmount;
        $this->total = $subtotal + $taxAmount - $discountAmount;

        return $this;
    }

    // Actions
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function approve($userId = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $userId ?? auth()->id(),
        ]);
    }

    public function reject($reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function convertToInvoice()
    {
        if (!$this->canBeConverted()) {
            return null;
        }

        $invoice = Invoice::create([
            'tenant_id' => $this->tenant_id,
            'client_id' => $this->client_id,
            'quotation_id' => $this->id,
            'created_by' => auth()->id(),
            'invoice_date' => now(),
            'due_date' => now()->addDays($this->client->payment_term_days ?? 30),
            'subject' => $this->subject,
            'notes' => $this->notes,
            'terms' => $this->terms,
            'subtotal' => $this->subtotal,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'amount_due' => $this->total,
            'status' => 'draft',
        ]);

        foreach ($this->items as $item) {
            $invoice->items()->create([
                'product_id' => $item->product_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'discount_percent' => $item->discount_percent,
                'tax_percent' => $item->tax_percent,
                'tax_amount' => $item->tax_amount,
                'subtotal' => $item->subtotal,
                'sort_order' => $item->sort_order,
            ]);
        }

        $this->update(['status' => 'converted']);

        return $invoice;
    }
}

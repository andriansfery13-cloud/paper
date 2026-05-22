<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use App\Traits\HasVerificationCode;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, LogsActivity, HasVerificationCode;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'quotation_id',
        'created_by',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subject',
        'notes',
        'terms',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_amount',
        'shipping_amount',
        'total',
        'amount_paid',
        'amount_due',
        'status',
        'is_recurring',
        'recurring_period',
        'next_recurring_date',
        'recurring_end_date',
        'currency',
        'exchange_rate',
        'document_hash',
        'verification_code',
        'signature_image',
        'stamp_image',
        'qr_payment_code',
        'payment_link',
        'sent_at',
        'viewed_at',
        'paid_at',
        'cancelled_at',
        'last_reminder_at',
        'reminder_count',
        'include_signature',
        'include_stamp',
        'include_qr',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'next_recurring_date' => 'date',
        'recurring_end_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'is_recurring' => 'boolean',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_reminder_at' => 'datetime',
        'include_signature' => 'boolean',
        'include_stamp' => 'boolean',
        'include_qr' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = static::generateNumber($invoice->tenant_id);
            }
            $invoice->amount_due = $invoice->total - ($invoice->amount_paid ?? 0);
        });

        static::updating(function ($invoice) {
            $invoice->amount_due = $invoice->total - $invoice->amount_paid;
            $invoice->updateStatus();
        });
    }

    public static function generateNumber($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        $prefix = $tenant ? $tenant->invoice_prefix : 'INV';
        $year = date('Y');
        $month = date('m');

        $lastInvoice = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -5) + 1 : 1;
        return "{$prefix}/{$year}{$month}/" . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }

    // Status Methods
    public function updateStatus()
    {
        if ($this->amount_due <= 0 && $this->total > 0) {
            $this->status = 'paid';
            $this->paid_at = $this->paid_at ?? now();
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partial';
        } elseif ($this->due_date < now() && in_array($this->status, ['sent', 'viewed'])) {
            $this->status = 'overdue';
        }
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue' ||
            ($this->due_date < now() && !$this->isPaid());
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

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'viewed', 'partial', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', ['sent', 'viewed', 'partial']);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)])
            ->whereIn('status', ['sent', 'viewed', 'partial']);
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedAmountDueAttribute()
    {
        return 'Rp ' . number_format($this->amount_due, 0, ',', '.');
    }

    public function getVerificationUrlAttribute()
    {
        return route('verify.invoice', $this->verification_code);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'sent' => 'info',
            'viewed' => 'primary',
            'partial' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'dark',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    // Calculation Methods
    public function calculateTotals()
    {
        $subtotal = $this->items->sum('subtotal');
        $taxAmount = $this->items->sum('tax_amount');

        $discountAmount = 0;
        if ($this->discount_type == 1) { // Percentage
            $discountAmount = $subtotal * ($this->discount_value / 100);
        } else {
            $discountAmount = $this->discount_value;
        }

        $this->subtotal = $subtotal;
        $this->tax_amount = $taxAmount;
        $this->discount_amount = $discountAmount;
        $this->total = $subtotal + $taxAmount - $discountAmount + ($this->shipping_amount ?? 0);
        $this->amount_due = $this->total - $this->amount_paid;

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

    public function markAsViewed()
    {
        if ($this->status === 'sent') {
            $this->update([
                'status' => 'viewed',
                'viewed_at' => now(),
            ]);
        }
    }

    public function recordPayment($amount)
    {
        $this->increment('amount_paid', $amount);
        $this->amount_due = $this->total - $this->amount_paid;
        $this->updateStatus();
        $this->save();

        // Update client outstanding
        $this->client->updateOutstandingReceivable();
    }
}

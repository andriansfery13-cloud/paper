<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'received_by',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'gateway_transaction_id',
        'gateway_response',
        'gateway_fee',
        'notes',
        'status',
        'proof_of_payment',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->payment_number) {
                $payment->payment_number = static::generateNumber($payment->tenant_id);
            }
        });

        static::created(function ($payment) {
            if ($payment->status === 'success') {
                $payment->invoice->recordPayment($payment->amount);

                // Create receipt automatically
                $payment->createReceipt();

                // Create income transaction
                $payment->createIncomeTransaction();
            }
        });
    }

    public static function generateNumber($tenantId)
    {
        $year = date('Y');
        $month = date('m');

        $lastPayment = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastPayment ? (int) substr($lastPayment->payment_number, -5) + 1 : 1;
        return "PAY/{$year}{$month}/" . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function incomeTransaction()
    {
        return $this->hasOne(IncomeTransaction::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'success' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            'expired' => 'secondary',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cash' => 'Tunai',
            'bank_transfer' => 'Transfer Bank',
            'credit_card' => 'Kartu Kredit',
            'va' => 'Virtual Account',
            'qris' => 'QRIS',
            'ewallet' => 'E-Wallet',
        ];
        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    // Methods
    public function markAsSuccess()
    {
        $this->update(['status' => 'success']);
        $this->invoice->recordPayment($this->amount);
        $this->createReceipt();
        $this->createIncomeTransaction();
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }

    public function createReceipt()
    {
        if ($this->receipt) {
            return $this->receipt;
        }

        return Receipt::create([
            'tenant_id' => $this->tenant_id,
            'invoice_id' => $this->invoice_id,
            'payment_id' => $this->id,
            'created_by' => $this->received_by ?? auth()->id(),
            'receipt_date' => $this->payment_date,
            'amount' => $this->amount,
        ]);
    }

    public function createIncomeTransaction()
    {
        if ($this->incomeTransaction) {
            return $this->incomeTransaction;
        }

        return IncomeTransaction::create([
            'tenant_id' => $this->tenant_id,
            'payment_id' => $this->id,
            'created_by' => $this->received_by ?? auth()->id(),
            'reference_number' => $this->payment_number,
            'transaction_date' => $this->payment_date,
            'description' => 'Pembayaran Invoice #' . $this->invoice->invoice_number,
            'amount' => $this->amount - $this->gateway_fee,
            'source' => 'invoice_payment',
            'payment_method' => $this->payment_method,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class SupplierPayable extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'created_by',
        'reference_number',
        'transaction_date',
        'due_date',
        'description',
        'amount',
        'amount_paid',
        'amount_due',
        'status',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payable) {
            if (!$payable->reference_number) {
                $payable->reference_number = static::generateNumber($payable->tenant_id);
            }
            $payable->amount_due = $payable->amount - ($payable->amount_paid ?? 0);
        });

        static::updating(function ($payable) {
            $payable->amount_due = $payable->amount - $payable->amount_paid;
            $payable->updateStatus();
        });
    }

    public static function generateNumber($tenantId)
    {
        $year = date('Y');
        $month = date('m');

        $lastPayable = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastPayable ? (int) substr($lastPayable->reference_number, -5) + 1 : 1;
        return "PAY/{$year}{$month}/" . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayablePayment::class, 'payable_id');
    }

    // Status Methods
    public function updateStatus()
    {
        if ($this->amount_due <= 0 && $this->amount > 0) {
            $this->status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partial';
        } elseif ($this->due_date < now() && $this->status !== 'paid') {
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

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['unpaid', 'partial', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', ['unpaid', 'partial']);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)])
            ->whereIn('status', ['unpaid', 'partial']);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedAmountDueAttribute()
    {
        return 'Rp ' . number_format($this->amount_due, 0, ',', '.');
    }

    public function getFormattedAmountPaidAttribute()
    {
        return 'Rp ' . number_format($this->amount_paid, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'unpaid' => 'warning',
            'partial' => 'info',
            'paid' => 'success',
            'overdue' => 'danger',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'unpaid' => 'Belum Dibayar',
            'partial' => 'Dibayar Sebagian',
            'paid' => 'Lunas',
            'overdue' => 'Jatuh Tempo',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    // Actions
    public function recordPayment($amount, $paymentMethod, $referenceNumber = null, $notes = null)
    {
        $payment = $this->payments()->create([
            'tenant_id' => $this->tenant_id,
            'created_by' => auth()->id(),
            'payment_date' => now(),
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
        ]);

        $this->increment('amount_paid', $amount);
        $this->amount_due = $this->amount - $this->amount_paid;
        $this->updateStatus();
        $this->save();

        // Create expense transaction
        ExpenseTransaction::create([
            'tenant_id' => $this->tenant_id,
            'supplier_id' => $this->supplier_id,
            'created_by' => auth()->id(),
            'reference_number' => $payment->reference_number ?? $this->reference_number,
            'transaction_date' => now(),
            'description' => 'Pembayaran Hutang - ' . $this->description,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'notes' => $notes,
        ]);

        return $payment;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'npwp',
        'contact_person',
        'contact_phone',
        'payment_term_days',
        'credit_limit',
        'outstanding_payable',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'outstanding_payable' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (!$supplier->code) {
                $supplier->code = static::generateCode($supplier->tenant_id);
            }
        });
    }

    public static function generateCode($tenantId)
    {
        $lastSupplier = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastSupplier ? (int) substr($lastSupplier->code, 4) + 1 : 1;
        return 'SUP-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function payables()
    {
        return $this->hasMany(SupplierPayable::class);
    }

    public function expenseTransactions()
    {
        return $this->hasMany(ExpenseTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedOutstandingAttribute()
    {
        return 'Rp ' . number_format($this->outstanding_payable, 0, ',', '.');
    }

    public function updateOutstandingPayable()
    {
        $total = $this->payables()
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->sum('amount_due');

        $this->update(['outstanding_payable' => $total]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;

class Client extends Model
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
        'contact_email',
        'payment_term_days',
        'credit_limit',
        'outstanding_receivable',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'outstanding_receivable' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (!$client->code) {
                $client->code = static::generateCode($client->tenant_id);
            }
        });
    }

    public static function generateCode($tenantId)
    {
        $lastClient = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastClient ? (int) substr($lastClient->code, 4) + 1 : 1;
        return 'CLT-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function unpaidInvoices()
    {
        return $this->hasMany(Invoice::class)->whereIn('status', ['sent', 'partial', 'overdue']);
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
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedOutstandingAttribute()
    {
        return 'Rp ' . number_format($this->outstanding_receivable, 0, ',', '.');
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code,
            $this->country
        ]);
        return implode(', ', $parts);
    }

    // Methods
    public function updateOutstandingReceivable()
    {
        $total = $this->invoices()
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->sum('amount_due');

        $this->update(['outstanding_receivable' => $total]);
    }

    public function hasExceededCreditLimit()
    {
        if ($this->credit_limit <= 0) {
            return false;
        }
        return $this->outstanding_receivable >= $this->credit_limit;
    }
}

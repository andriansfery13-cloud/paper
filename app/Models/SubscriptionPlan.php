<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'max_invoices',
        'max_clients',
        'max_users',
        'max_products',
        'max_quotations',
        'has_payment_gateway',
        'has_api_access',
        'has_custom_template',
        'has_recurring_invoice',
        'has_multi_currency',
        'menu_permissions',
        'included_tokens',
        'trial_days',
        'is_active',
        'sort_order',
        'has_wa_gateway',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'has_payment_gateway' => 'boolean',
        'has_api_access' => 'boolean',
        'has_custom_template' => 'boolean',
        'has_recurring_invoice' => 'boolean',
        'has_multi_currency' => 'boolean',
        'has_wa_gateway' => 'boolean',
        'is_active' => 'boolean',
        'menu_permissions' => 'array',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'current_plan_id');
    }

    public function subscriptionHistories()
    {
        return $this->hasMany(SubscriptionHistory::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getFormattedPriceMonthlyAttribute()
    {
        return 'Rp ' . number_format($this->price_monthly, 0, ',', '.');
    }

    public function getFormattedPriceYearlyAttribute()
    {
        return 'Rp ' . number_format($this->price_yearly, 0, ',', '.');
    }

    public function isUnlimited($feature)
    {
        $value = $this->{"max_{$feature}"} ?? -1;
        return $value === -1;
    }
}

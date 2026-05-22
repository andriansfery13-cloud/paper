<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'npwp',
        'logo',
        'stamp_image',
        'signature_image',
        'website',
        'settings',
        'invoice_settings',
        'invoice_prefix',
        'quotation_prefix',
        'receipt_prefix',
        'delivery_prefix',
        'status',
        'current_plan_id',
        'trial_ends_at',
        'subscription_ends_at',
        'token_balance',
        'timezone',
        'currency',
        'date_format',
        'email_verified_at',
        'phone_verified_at',
        'email_otp',
        'phone_otp',
        'email_otp_expires_at',
        'phone_otp_expires_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'invoice_settings' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'email_otp_expires_at' => 'datetime',
        'phone_otp_expires_at' => 'datetime',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function owner()
    {
        return $this->hasOne(User::class)->where('is_owner', true);
    }

    public function currentPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'current_plan_id');
    }

    public function subscriptionHistories()
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documentTemplates()
    {
        return $this->hasMany(DocumentTemplate::class);
    }

    // Subscription Methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription()
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    public function isSubscriptionValid()
    {
        return $this->isOnTrial() || $this->hasActiveSubscription();
    }

    public function daysUntilExpiry()
    {
        if ($this->isOnTrial()) {
            return $this->trial_ends_at->diffInDays(now());
        }
        if ($this->hasActiveSubscription()) {
            return $this->subscription_ends_at->diffInDays(now());
        }
        return 0;
    }

    // Token Methods
    public function hasTokens($amount = 1)
    {
        return $this->token_balance >= $amount;
    }

    public function deductTokens($amount, $description, $reference = null)
    {
        if (!$this->hasTokens($amount)) {
            return false;
        }

        $this->decrement('token_balance', $amount);

        TokenTransaction::create([
            'tenant_id' => $this->id,
            'user_id' => auth()->id(),
            'amount' => $amount,
            'type' => 'debit',
            'description' => $description,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'balance_after' => $this->token_balance,
        ]);

        return true;
    }

    public function addTokens($amount, $description, $reference = null)
    {
        $this->increment('token_balance', $amount);

        TokenTransaction::create([
            'tenant_id' => $this->id,
            'user_id' => auth()->id(),
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'balance_after' => $this->token_balance,
        ]);

        return true;
    }

    // Limit checking methods
    public function canCreateInvoice()
    {
        $plan = $this->currentPlan;
        if (!$plan || $plan->max_invoices === -1) {
            return true;
        }
        return $this->invoices()->count() < $plan->max_invoices;
    }

    public function canCreateClient()
    {
        $plan = $this->currentPlan;
        if (!$plan || $plan->max_clients === -1) {
            return true;
        }
        return $this->clients()->count() < $plan->max_clients;
    }

    public function canCreateUser()
    {
        $plan = $this->currentPlan;
        if (!$plan || $plan->max_users === -1) {
            return true;
        }
        return $this->users()->count() < $plan->max_users;
    }

    public function canCreateProduct()
    {
        $plan = $this->currentPlan;
        if (!$plan || $plan->max_products === -1) {
            return true;
        }
        return $this->products()->count() < $plan->max_products;
    }

    public function canCreateQuotation()
    {
        $plan = $this->currentPlan;
        if (!$plan || $plan->max_quotations === -1) {
            return true;
        }
        return $this->quotations()->count() < $plan->max_quotations;
    }

    public function canUsePaymentGateway()
    {
        return $this->currentPlan && $this->currentPlan->has_payment_gateway;
    }

    public function canUseApi()
    {
        return $this->currentPlan && $this->currentPlan->has_api_access;
    }

    /**
     * Check if tenant has access to specific menu based on subscription plan
     */
    public function hasMenuAccess($menuKey)
    {
        // If no plan, allow dashboard & settings only
        if (!$this->currentPlan) {
            return in_array($menuKey, ['dashboard', 'settings']);
        }

        $permissions = $this->currentPlan->menu_permissions;

        // If permissions is null/empty (legacy plans), allow all or handle as needed. 
        // For security, let's assume if it's explicitly set, we respect it.
        // If null, we might want to default to true for backward compatibility or false for strictness.
        // Given the user issue, likely they WANT strictness.
        // But if seeder failed to populate, this might break things.
        // We will seed properly. For now, strict check.

        if (empty($permissions)) {
            // Fallback: if "Enterprise", "Pro" etc are hardcoded names, we could map them.
            // But better to just fix the data.
            // Let's return false if not in the list, unless it's dashboard/settings which are always required.
            return in_array($menuKey, ['dashboard', 'settings']);
        }

        return in_array($menuKey, $permissions);
    }

    /**
     * Get quota status for all limits
     */
    public function getQuotaStatus()
    {
        $plan = $this->currentPlan;
        return [
            'invoices' => [
                'used' => $this->invoices()->count(),
                'max' => $plan ? $plan->max_invoices : 0,
                'label' => 'Invoice',
            ],
            'clients' => [
                'used' => $this->clients()->count(),
                'max' => $plan ? $plan->max_clients : 0,
                'label' => 'Client',
            ],
            'users' => [
                'used' => $this->users()->count(),
                'max' => $plan ? $plan->max_users : 0,
                'label' => 'User',
            ],
            'products' => [
                'used' => $this->products()->count(),
                'max' => $plan ? $plan->max_products : 0,
                'label' => 'Produk',
            ],
            'quotations' => [
                'used' => $this->quotations()->count(),
                'max' => $plan ? $plan->max_quotations : 0,
                'label' => 'Penawaran',
            ],
        ];
    }

    public function canUseWaGateway()
    {
        // If we want to allow based purely on plan capability:
        return $this->currentPlan && $this->currentPlan->has_wa_gateway;
    }
}

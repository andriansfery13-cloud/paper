<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Cocok untuk memulai dan mencoba fitur dasar',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_invoices' => 10,
                'max_clients' => 5,
                'max_users' => 1,
                'max_products' => 20,
                'max_quotations' => 5,
                'has_payment_gateway' => false,
                'has_api_access' => false,
                'has_custom_template' => false,
                'has_recurring_invoice' => false,
                'has_multi_currency' => false,
                'menu_permissions' => ['dashboard', 'products', 'invoices', 'settings'],
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Untuk bisnis kecil yang sedang berkembang',
                'price_monthly' => 99000,
                'price_yearly' => 990000,
                'max_invoices' => 100,
                'max_clients' => 50,
                'max_users' => 3,
                'max_products' => 100,
                'max_quotations' => 50,
                'has_payment_gateway' => true,
                'has_api_access' => false,
                'has_custom_template' => false,
                'has_recurring_invoice' => false,
                'has_multi_currency' => false,
                'included_tokens' => 100,
                'menu_permissions' => ['dashboard', 'clients', 'products', 'invoices', 'quotations', 'payments', 'receipts', 'settings'],
                'trial_days' => 14,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Untuk bisnis menengah dengan kebutuhan lengkap',
                'price_monthly' => 249000,
                'price_yearly' => 2490000,
                'max_invoices' => 500,
                'max_clients' => 200,
                'max_users' => 10,
                'max_products' => 500,
                'max_quotations' => 200,
                'has_payment_gateway' => true,
                'has_api_access' => true,
                'has_custom_template' => true,
                'has_recurring_invoice' => true,
                'has_multi_currency' => false,
                'included_tokens' => 500,
                'menu_permissions' => ['dashboard', 'clients', 'products', 'invoices', 'quotations', 'payments', 'receipts', 'delivery_notes', 'expenses', 'income', 'reports', 'templates', 'settings', 'users'],
                'trial_days' => 14,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Untuk perusahaan besar dengan kebutuhan tanpa batas',
                'price_monthly' => 599000,
                'price_yearly' => 5990000,
                'max_invoices' => -1, // Unlimited
                'max_clients' => -1,
                'max_users' => -1,
                'max_products' => -1,
                'max_quotations' => -1,
                'has_payment_gateway' => true,
                'has_api_access' => true,
                'has_custom_template' => true,
                'has_recurring_invoice' => true,
                'has_multi_currency' => true,
                'included_tokens' => 2000,
                'menu_permissions' => ['dashboard', 'clients', 'products', 'invoices', 'quotations', 'payments', 'receipts', 'delivery_notes', 'expenses', 'income', 'reports', 'templates', 'settings', 'users'],
                'trial_days' => 30,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin (Platform Owner)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@paper.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'user_type' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Get Pro plan for demo tenant
        $proPlan = SubscriptionPlan::where('slug', 'pro')->first();

        // Create Demo Tenant
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'demo-company'],
            [
                'company_name' => 'PT Demo Company',
                'email' => 'admin@demo-company.com',
                'phone' => '021-12345678',
                'address' => 'Jl. Sudirman No. 123',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12345',
                'npwp' => '12.345.678.9-012.345',
                'status' => 'active',
                'current_plan_id' => $proPlan ? $proPlan->id : null,
                'subscription_ends_at' => now()->addYear(),
                'token_balance' => 500,
                'invoice_prefix' => 'INV',
                'quotation_prefix' => 'QUO',
                'receipt_prefix' => 'REC',
                'delivery_prefix' => 'DO',
            ]
        );

        // Create Demo Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@demo-company.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Admin Demo',
                'password' => Hash::make('password'),
                'user_type' => 'tenant_user',
                'is_owner' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('owner');

        // Create Finance Staff
        $financeUser = User::firstOrCreate(
            ['email' => 'finance@demo-company.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Staff Keuangan',
                'password' => Hash::make('password'),
                'user_type' => 'tenant_user',
                'is_owner' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $financeUser->assignRole('finance');

        // Create Product Categories
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Barang elektronik'],
            ['name' => 'Komputer & Aksesoris', 'description' => 'Komputer, laptop, dan aksesoris'],
            ['name' => 'Furnitur', 'description' => 'Meja, kursi, lemari'],
            ['name' => 'Jasa', 'description' => 'Layanan jasa'],
        ];

        foreach ($categories as $cat) {
            ProductCategory::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $cat['name']],
                array_merge($cat, ['tenant_id' => $tenant->id])
            );
        }

        // Create Demo Products
        $products = [
            ['name' => 'Laptop Asus ROG', 'selling_price' => 15000000, 'purchase_price' => 13000000, 'stock' => 10],
            ['name' => 'Monitor LG 27 inch', 'selling_price' => 3500000, 'purchase_price' => 3000000, 'stock' => 25],
            ['name' => 'Keyboard Mechanical', 'selling_price' => 850000, 'purchase_price' => 600000, 'stock' => 50],
            ['name' => 'Mouse Gaming', 'selling_price' => 450000, 'purchase_price' => 300000, 'stock' => 100],
            ['name' => 'Meja Kerja', 'selling_price' => 1200000, 'purchase_price' => 800000, 'stock' => 15],
            ['name' => 'Kursi Ergonomis', 'selling_price' => 2500000, 'purchase_price' => 1800000, 'stock' => 20],
            ['name' => 'Jasa Instalasi', 'selling_price' => 500000, 'purchase_price' => 0, 'stock' => 0],
            ['name' => 'Jasa Maintenance', 'selling_price' => 350000, 'purchase_price' => 0, 'stock' => 0],
        ];

        $category = ProductCategory::where('tenant_id', $tenant->id)->first();
        foreach ($products as $prod) {
            Product::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $prod['name']],
                array_merge($prod, [
                    'tenant_id' => $tenant->id,
                    'category_id' => $category->id,
                    'unit' => 'pcs',
                    'tax_rate' => 11,
                    'is_taxable' => true,
                    'is_active' => true,
                    'track_stock' => $prod['stock'] > 0,
                ])
            );
        }

        // Create Demo Clients
        $clients = [
            [
                'name' => 'PT Maju Jaya',
                'email' => 'purchasing@majujaya.com',
                'phone' => '021-11111111',
                'address' => 'Jl. Gatot Subroto No. 100',
                'city' => 'Jakarta',
                'payment_term_days' => 30,
            ],
            [
                'name' => 'CV Sukses Makmur',
                'email' => 'admin@suksesmakmur.com',
                'phone' => '021-22222222',
                'address' => 'Jl. Rasuna Said No. 50',
                'city' => 'Jakarta',
                'payment_term_days' => 14,
            ],
            [
                'name' => 'PT Teknologi Nusantara',
                'email' => 'info@teknusa.com',
                'phone' => '021-33333333',
                'address' => 'Jl. Kuningan No. 75',
                'city' => 'Jakarta',
                'payment_term_days' => 30,
            ],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(
                ['tenant_id' => $tenant->id, 'email' => $client['email']],
                array_merge($client, [
                    'tenant_id' => $tenant->id,
                    'is_active' => true,
                ])
            );
        }

        // Create Expense Categories
        $expenseCategories = [
            ['name' => 'Operasional', 'color' => '#3B82F6'],
            ['name' => 'Gaji & Tunjangan', 'color' => '#10B981'],
            ['name' => 'Sewa & Utilitas', 'color' => '#F59E0B'],
            ['name' => 'Marketing', 'color' => '#EF4444'],
            ['name' => 'Transportasi', 'color' => '#8B5CF6'],
            ['name' => 'Lainnya', 'color' => '#6B7280'],
        ];

        foreach ($expenseCategories as $cat) {
            ExpenseCategory::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $cat['name']],
                array_merge($cat, ['tenant_id' => $tenant->id])
            );
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Super Admin: superadmin@paper.test / password');
        $this->command->info('Tenant Admin: admin@demo-company.com / password');
    }
}

<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    /**
     * Available menu items that can be enabled/disabled per plan
     */
    public static function getAvailableMenus()
    {
        return [
            'dashboard' => ['label' => 'Dashboard', 'icon' => 'home', 'required' => true],
            'invoices' => ['label' => 'Invoice', 'icon' => 'document-text', 'required' => false],
            'quotations' => ['label' => 'Penawaran', 'icon' => 'clipboard-list', 'required' => false],
            'clients' => ['label' => 'Client', 'icon' => 'users', 'required' => false],
            'products' => ['label' => 'Produk & Layanan', 'icon' => 'cube', 'required' => false],
            'payments' => ['label' => 'Pembayaran', 'icon' => 'credit-card', 'required' => false],
            'receipts' => ['label' => 'Kwitansi', 'icon' => 'receipt', 'required' => false],
            'delivery_notes' => ['label' => 'Surat Jalan', 'icon' => 'truck', 'required' => false],
            'expenses' => ['label' => 'Pengeluaran', 'icon' => 'trending-down', 'required' => false],
            'income' => ['label' => 'Pemasukan', 'icon' => 'trending-up', 'required' => false],
            'reports' => ['label' => 'Laporan', 'icon' => 'chart-bar', 'required' => false],
            'templates' => ['label' => 'Template Dokumen', 'icon' => 'template', 'required' => false],
            'settings' => ['label' => 'Pengaturan', 'icon' => 'cog', 'required' => true],
            'users' => ['label' => 'Kelola User', 'icon' => 'user-group', 'required' => false],
        ];
    }

    public function __construct()
    {
        $this->middleware(['auth', 'superadmin']);
    }

    public function index()
    {
        $plans = SubscriptionPlan::withCount([
            'tenants' => function ($q) {
                $q->where('status', 'active');
            }
        ])->ordered()->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function create()
    {
        $menus = self::getAvailableMenus();
        return view('superadmin.plans.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'nullable|numeric|min:0',
            'max_invoices' => 'required|integer|min:-1',
            'max_clients' => 'required|integer|min:-1',
            'max_users' => 'required|integer|min:-1',
            'max_products' => 'required|integer|min:-1',
            'max_quotations' => 'required|integer|min:-1',
            'has_payment_gateway' => 'boolean',
            'has_wa_gateway' => 'boolean',
            'has_api_access' => 'boolean',
            'has_custom_template' => 'boolean',
            'has_recurring_invoice' => 'boolean',
            'has_multi_currency' => 'boolean',
            'menu_permissions' => 'array',
            'included_tokens' => 'nullable|integer|min:0',
            'trial_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Ensure required menus are always included
        $menus = $request->input('menu_permissions', []);
        $menus[] = 'dashboard';
        $menus[] = 'settings';
        $validated['menu_permissions'] = array_unique($menus);

        // Set defaults
        $validated['price_yearly'] = $validated['price_yearly'] ?? ($validated['price_monthly'] * 10);
        $validated['has_payment_gateway'] = $request->boolean('has_payment_gateway');
        $validated['has_wa_gateway'] = $request->boolean('has_wa_gateway');
        $validated['has_api_access'] = $request->boolean('has_api_access');
        $validated['has_custom_template'] = $request->boolean('has_custom_template');
        $validated['has_recurring_invoice'] = $request->boolean('has_recurring_invoice');
        $validated['has_multi_currency'] = $request->boolean('has_multi_currency');
        $validated['is_active'] = $request->boolean('is_active', true);

        SubscriptionPlan::create($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Paket langganan berhasil dibuat.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        $menus = self::getAvailableMenus();
        return view('superadmin.plans.edit', compact('plan', 'menus'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'nullable|numeric|min:0',
            'max_invoices' => 'required|integer|min:-1',
            'max_clients' => 'required|integer|min:-1',
            'max_users' => 'required|integer|min:-1',
            'max_products' => 'required|integer|min:-1',
            'max_quotations' => 'required|integer|min:-1',
            'has_payment_gateway' => 'boolean',
            'has_wa_gateway' => 'boolean',
            'has_api_access' => 'boolean',
            'has_custom_template' => 'boolean',
            'has_recurring_invoice' => 'boolean',
            'has_multi_currency' => 'boolean',
            'menu_permissions' => 'array',
            'included_tokens' => 'nullable|integer|min:0',
            'trial_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Ensure required menus are always included
        $menus = $request->input('menu_permissions', []);
        $menus[] = 'dashboard';
        $menus[] = 'settings';
        $validated['menu_permissions'] = array_unique($menus);

        // Set booleans
        $validated['has_payment_gateway'] = $request->boolean('has_payment_gateway');
        $validated['has_wa_gateway'] = $request->boolean('has_wa_gateway');
        $validated['has_api_access'] = $request->boolean('has_api_access');
        $validated['has_custom_template'] = $request->boolean('has_custom_template');
        $validated['has_recurring_invoice'] = $request->boolean('has_recurring_invoice');
        $validated['has_multi_currency'] = $request->boolean('has_multi_currency');
        $validated['is_active'] = $request->boolean('is_active', true);

        $plan->update($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Paket langganan berhasil diupdate.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        // Check if plan has active tenants
        if ($plan->tenants()->where('status', 'active')->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus paket yang masih memiliki tenant aktif.');
        }

        $plan->delete();

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Paket langganan berhasil dihapus.');
    }
}

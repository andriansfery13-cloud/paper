<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionHistory;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'superadmin']);
    }

    public function index()
    {
        $stats = $this->getDashboardStats();
        $recentTenants = $this->getRecentTenants();
        $subscriptionDistribution = $this->getSubscriptionDistribution();
        $tenantGrowth = $this->getTenantGrowthData();
        $revenueData = $this->getRevenueData();
        $recentActivities = $this->getRecentSystemActivities();

        return view('superadmin.dashboard', compact(
            'stats',
            'recentTenants',
            'subscriptionDistribution',
            'tenantGrowth',
            'revenueData',
            'recentActivities'
        ));
    }

    private function getDashboardStats()
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Tenant stats
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $suspendedTenants = Tenant::where('status', 'suspended')->count();
        $newTenantsThisMonth = Tenant::where('created_at', '>=', $currentMonth)->count();
        $newTenantsLastMonth = Tenant::whereBetween('created_at', [$lastMonth, $currentMonth])->count();

        // User stats
        $totalUsers = User::where('user_type', 'tenant_user')->count();
        $newUsersThisMonth = User::where('user_type', 'tenant_user')
            ->where('created_at', '>=', $currentMonth)
            ->count();

        // Revenue stats
        $validStatuses = ['active', 'paid', 'expired', 'upgraded'];

        $totalRevenue = SubscriptionHistory::whereIn('status', $validStatuses)->sum('amount_paid');
        $monthlyRevenue = SubscriptionHistory::whereIn('status', $validStatuses)
            ->where('created_at', '>=', $currentMonth)
            ->sum('amount_paid');
        $lastMonthRevenue = SubscriptionHistory::whereIn('status', $validStatuses)
            ->whereBetween('created_at', [$lastMonth, $currentMonth])
            ->sum('amount_paid');

        // Plan distribution
        $planDistribution = Tenant::select('current_plan_id', DB::raw('count(*) as total'))
            ->groupBy('current_plan_id')
            ->get();

        // Calculate growth rates
        $tenantGrowth = $newTenantsLastMonth > 0
            ? round(($newTenantsThisMonth - $newTenantsLastMonth) / $newTenantsLastMonth * 100, 1)
            : ($newTenantsThisMonth > 0 ? 100 : 0);

        $revenueGrowth = $lastMonthRevenue > 0
            ? round(($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100, 1)
            : ($monthlyRevenue > 0 ? 100 : 0);

        return [
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'suspended_tenants' => $suspendedTenants,
            'new_tenants_this_month' => $newTenantsThisMonth,
            'tenant_growth' => $tenantGrowth,
            'total_users' => $totalUsers,
            'new_users_this_month' => $newUsersThisMonth,
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'revenue_growth' => $revenueGrowth,
        ];
    }

    private function getRecentTenants()
    {
        return Tenant::with(['owner', 'currentPlan'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    private function getSubscriptionDistribution()
    {
        return SubscriptionPlan::withCount([
            'tenants' => function ($query) {
                $query->where('status', 'active');
            }
        ])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($plan) {
                return [
                    'name' => $plan->name,
                    'count' => $plan->tenants_count,
                    'color' => $this->getPlanColor($plan->slug ?? $plan->name),
                ];
            });
    }

    private function getPlanColor($planName)
    {
        $colors = [
            'free' => '#94a3b8',
            'starter' => '#3b82f6',
            'professional' => '#8b5cf6',
            'enterprise' => '#f59e0b',
        ];
        return $colors[strtolower($planName)] ?? '#6b7280';
    }

    private function getTenantGrowthData()
    {
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Tenant::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $data[] = [
                'month' => $month->format('M Y'),
                'count' => $count,
            ];
        }

        return $data;
    }

    private function getRevenueData()
    {
        $data = [];
        $validStatuses = ['active', 'paid', 'expired', 'upgraded'];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = SubscriptionHistory::whereIn('status', $validStatuses)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount_paid');

            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
            ];
        }

        return $data;
    }

    private function getRecentSystemActivities()
    {
        return ActivityLog::with(['user', 'tenant'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();
    }

    public function chartData(Request $request)
    {
        $type = $request->get('type', 'tenants');

        switch ($type) {
            case 'tenants':
                return response()->json($this->getTenantGrowthData());
            case 'revenue':
                return response()->json($this->getRevenueData());
            case 'distribution':
                return response()->json($this->getSubscriptionDistribution());
            default:
                return response()->json([]);
        }
    }

    // Tenant Management
    public function showTenant(Tenant $tenant)
    {
        $tenant->load(['owner', 'users', 'currentPlan', 'subscriptionHistories']);

        // Get tenant-specific stats
        $stats = [
            'total_users' => $tenant->users()->count(),
            'total_invoices' => DB::table('invoices')->where('tenant_id', $tenant->id)->count(),
            'total_clients' => DB::table('clients')->where('tenant_id', $tenant->id)->count(),
            'total_revenue' => DB::table('payments')
                ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->where('invoices.tenant_id', $tenant->id)
                ->where('payments.status', 'success')
                ->sum('payments.amount'),
        ];

        return view('superadmin.tenants.show', compact('tenant', 'stats'));
    }

    public function editTenant(Tenant $tenant)
    {
        $plans = SubscriptionPlan::ordered()->get();
        return view('superadmin.tenants.edit', compact('tenant', 'plans'));
    }

    public function updateTenant(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,suspended,cancelled',
            'current_plan_id' => 'nullable|exists:subscription_plans,id',
            'subscription_ends_at' => 'nullable|date',
        ]);

        $oldPlanId = $tenant->current_plan_id;
        $newPlanId = $validated['current_plan_id'];

        $tenant->fill($validated);

        // If plan changed, handle side effects
        if ($oldPlanId != $newPlanId && $newPlanId) {
            $plan = SubscriptionPlan::find($newPlanId);

            // 1. Update Token Balance (Reset or Add? Usually reset to plan limit for manual change)
            // Let's assume manual change by admin resets/sets to plan default
            $tenant->token_balance = $plan->included_tokens;

            // 2. Log History (Optional, but good for tracking)
            SubscriptionHistory::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'amount_paid' => 0, // Manual change by admin assumed free or handled externally
                'payment_method' => 'manual_admin',
                'status' => 'active', // Changed from 'success' to 'active' to match ENUM
                'started_at' => now(), // Correct column name
                'ended_at' => $validated['subscription_ends_at'] ?? now()->addMonth(), // Correct column name
            ]);
        }

        $tenant->save();

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Tenant berhasil diupdate.' . ($oldPlanId != $newPlanId ? ' Paket langganan dan token telah diperbarui.' : ''));
    }

    public function suspendTenant(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);

        return redirect()->back()
            ->with('success', 'Tenant berhasil disuspend.');
    }

    public function activateTenant(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Tenant berhasil diaktifkan.');
    }

    public function impersonate(Tenant $tenant)
    {
        $owner = $tenant->owner;

        if (!$owner) {
            return back()->with('error', 'Tenant ini tidak memiliki owner yang valid.');
        }

        // Impersonate
        \Illuminate\Support\Facades\Auth::login($owner);

        // Update last login
        $owner->updateLastLogin();
        request()->session()->regenerate();

        return redirect()->route('dashboard')->with('success', "Login berhasil sebagai {$owner->name}");
    }
}

<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionHistory;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SubscriptionTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'superadmin']);
    }

    /**
     * Display subscription transactions list
     */
    public function index(Request $request)
    {
        $query = SubscriptionHistory::with(['tenant', 'plan'])
            ->latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%");
            })->orWhere('plan_name', 'like', "%{$search}%")
                ->orWhere('transaction_id', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('from_date')) {
            $query->whereDate('started_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('started_at', '<=', $request->to_date);
        }

        $transactions = $query->paginate(20);

        // Calculate stats
        $stats = [
            'total_revenue' => SubscriptionHistory::where('status', 'active')->sum('amount_paid'),
            'total_transactions' => SubscriptionHistory::count(),
            'this_month_revenue' => SubscriptionHistory::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount_paid'),
            'active_subscriptions' => Tenant::whereNotNull('current_plan_id')
                ->where('status', 'active')
                ->count(),
        ];

        return view('superadmin.subscriptions.index', compact('transactions', 'stats'));
    }

    /**
     * Show transaction details
     */
    public function show(SubscriptionHistory $subscription)
    {
        $subscription->load(['tenant', 'plan']);
        return view('superadmin.subscriptions.show', compact('subscription'));
    }

    /**
     * Verify subscription manually
     */
    public function verify(SubscriptionHistory $subscription)
    {
        if ($subscription->status === 'active') {
            return back()->with('error', 'Transaksi sudah aktif.');
        }

        // Activate subscription
        $tenant = $subscription->tenant;
        $tenant->current_plan_id = $subscription->plan_id;
        $tenant->subscription_ends_at = now()->addMonth();
        $tenant->status = 'active';
        $tenant->save();

        // Update history
        $subscription->update([
            'status' => 'active',
            'started_at' => now(),
            'ended_at' => now()->addMonth(),
            'notes' => 'Diverifikasi manual oleh SuperAdmin pada ' . now()->format('d M Y H:i'),
        ]);

        return back()->with('success', 'Langganan berhasil diverifikasi dan diaktifkan.');
    }

    /**
     * Delete subscription transaction
     */
    public function destroy(SubscriptionHistory $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'Riwayat transaksi berhasil dihapus.');
    }
}

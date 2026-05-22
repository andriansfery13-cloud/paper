<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransController extends Controller
{
    /**
     * Show subscription/pricing page
     */
    public function pricing()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->ordered()
            ->get();

        $tenant = auth()->user()->tenant;
        $currentPlan = $tenant ? $tenant->currentPlan : null;

        return view('subscription.pricing', compact('plans', 'currentPlan', 'tenant'));
    }

    /**
     * Create Midtrans Snap transaction
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Calculate price
        $price = $request->billing_period === 'yearly'
            ? ($plan->price_yearly ?: $plan->price_monthly * 10)
            : $plan->price_monthly;

        if ($price <= 0) {
            // Free plan - activate directly
            $this->activateSubscription($tenant, $plan, $request->billing_period);
            return redirect()->route('dashboard')->with('success', 'Paket berhasil diaktifkan!');
        }

        // Generate order ID
        $orderId = 'SUB-' . $tenant->id . '-' . time() . '-' . Str::random(4);

        // Store pending subscription
        $history = SubscriptionHistory::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'order_id' => $orderId,
            'billing_period' => $request->billing_period,
            'amount_paid' => $price,
            'payment_method' => 'midtrans',
            'status' => 'pending',
            'started_at' => now(),
        ]);

        // Midtrans configuration - use database settings or fallback to config
        $serverKey = \App\Models\Setting::get('midtrans_server_key', config('services.midtrans.server_key'));
        $isProduction = \App\Models\Setting::get('midtrans_is_production', config('services.midtrans.is_production')) === 'true';

        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$isProduction = $isProduction;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $price,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => $tenant->phone ?? '',
            ],
            'item_details' => [
                [
                    'id' => $plan->id,
                    'price' => (int) $price,
                    'quantity' => 1,
                    'name' => 'Paket ' . $plan->name . ' (' . ($request->billing_period === 'yearly' ? 'Tahunan' : 'Bulanan') . ')',
                ],
            ],
            'callbacks' => [
                'finish' => route('subscription.finish'),
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            return view('subscription.checkout', [
                'snapToken' => $snapToken,
                'plan' => $plan,
                'price' => $price,
                'billingPeriod' => $request->billing_period,
                'orderId' => $orderId,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Handle Midtrans notification callback
     */
    public function callback(Request $request)
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');

        try {
            $notification = new \Midtrans\Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $paymentType = $notification->payment_type;

            $history = SubscriptionHistory::where('order_id', $orderId)->first();

            if (!$history) {
                Log::warning('Midtrans callback: Order not found - ' . $orderId);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Update payment info
            $history->payment_method = $paymentType;
            $history->transaction_id = $notification->transaction_id ?? null;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $history->status = 'paid';
                    $history->paid_at = now();
                    $this->activateSubscription($history->tenant, $history->subscriptionPlan, $history->billing_period);
                } else {
                    $history->status = 'challenge';
                }
            } elseif ($transactionStatus == 'settlement') {
                $history->status = 'paid';
                $history->paid_at = now();
                $this->activateSubscription($history->tenant, $history->subscriptionPlan, $history->billing_period);
            } elseif ($transactionStatus == 'pending') {
                $history->status = 'pending';
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $history->status = 'failed';
            }

            $history->save();

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Midtrans callback error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle finish redirect from Midtrans
     */
    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $history = SubscriptionHistory::where('order_id', $orderId)->first();

        if ($history && $history->status === 'paid') {
            return redirect()->route('dashboard')->with('success', 'Pembayaran berhasil! Paket langganan Anda telah aktif.');
        }

        return redirect()->route('subscription.pricing')->with('warning', 'Pembayaran sedang diproses. Kami akan mengaktifkan paket Anda segera setelah pembayaran dikonfirmasi.');
    }

    /**
     * Activate subscription for tenant
     */
    protected function activateSubscription($tenant, $plan, $billingPeriod)
    {
        $duration = $billingPeriod === 'yearly' ? 365 : 30;

        $tenant->update([
            'current_plan_id' => $plan->id,
            'subscription_ends_at' => now()->addDays($duration),
            'status' => 'active',
            'token_balance' => $tenant->token_balance + ($plan->included_tokens ?? 0),
        ]);

        // Log activity
        if (class_exists('App\Models\ActivityLog')) {
            \App\Models\ActivityLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => $tenant->owner ? $tenant->owner->id : null,
                'action' => 'subscription_activated',
                'module' => 'subscription',
                'description' => 'Paket ' . $plan->name . ' diaktifkan (' . ($billingPeriod === 'yearly' ? 'Tahunan' : 'Bulanan') . ')',
            ]);
        }
    }

    /**
     * Get quota status for current tenant (API endpoint)
     */
    public function quotaStatus()
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return response()->json(['error' => 'No tenant'], 400);
        }

        $plan = $tenant->currentPlan;

        return response()->json([
            'plan_name' => $plan ? $plan->name : 'No Plan',
            'invoices' => [
                'used' => $tenant->invoices()->count(),
                'max' => $plan ? $plan->max_invoices : 0,
                'exceeded' => !$tenant->canCreateInvoice(),
            ],
            'clients' => [
                'used' => $tenant->clients()->count(),
                'max' => $plan ? $plan->max_clients : 0,
                'exceeded' => !$tenant->canCreateClient(),
            ],
            'users' => [
                'used' => $tenant->users()->count(),
                'max' => $plan ? $plan->max_users : 0,
                'exceeded' => !$tenant->canCreateUser(),
            ],
            'subscription_valid' => $tenant->isSubscriptionValid(),
            'days_until_expiry' => $tenant->daysUntilExpiry(),
        ]);
    }
}

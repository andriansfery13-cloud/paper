<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionHistory;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
    }

    public function index()
    {
        $tenant = auth()->user()->tenant;
        $tenant->load('currentPlan');

        $histories = $tenant->subscriptionHistories()
            ->with('plan')
            ->latest()
            ->limit(5)
            ->get();

        // Get all active subscription plans for upgrade/purchase
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)
            ->orderBy('price_monthly', 'asc')
            ->get();

        return view('settings.subscription.index', compact('tenant', 'histories', 'plans'));
    }

    public function history()
    {
        $tenant = auth()->user()->tenant;

        $histories = $tenant->subscriptionHistories()
            ->with('plan')
            ->latest()
            ->paginate(15);

        return view('settings.subscription.history', compact('tenant', 'histories'));
    }

    /**
     * Purchase/upgrade subscription plan
     */
    public function purchase(\App\Models\SubscriptionPlan $plan)
    {
        $tenant = auth()->user()->tenant;

        // If plan is free, directly activate it
        if ($plan->price_monthly == 0) {
            $tenant->current_plan_id = $plan->id;
            $tenant->subscription_ends_at = now()->addMonth();
            $tenant->status = 'active';
            $tenant->save();

            // Record in subscription history
            SubscriptionHistory::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'amount_paid' => 0,
                'started_at' => now(),
                'ended_at' => now()->addMonth(),
                'payment_method' => 'free',
                'status' => 'active',
            ]);

            return redirect()->route('settings.subscription')
                ->with('success', 'Berhasil mengaktifkan paket ' . $plan->name);
        }

        // For paid plans, create Midtrans transaction
        try {
            // Configure Midtrans from database settings
            $serverKey = \App\Models\Setting::get('midtrans_server_key', config('midtrans.server_key'));

            // Determine production mode: DB setting > Config/Env > Default false
            $dbProduction = \App\Models\Setting::get('midtrans_is_production');
            if ($dbProduction !== null) {
                $isProduction = $dbProduction === 'true';
            } else {
                $isProduction = config('midtrans.is_production', false);
            }

            if (empty($serverKey)) {
                return redirect()->route('settings.subscription')
                    ->with('error', 'Midtrans belum dikonfigurasi. Hubungi administrator.');
            }

            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = $isProduction;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $orderId = 'SUB-' . $tenant->id . '-' . $plan->id . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $plan->price_monthly,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone' => $tenant->phone ?? '',
                ],
                'item_details' => [
                    [
                        'id' => $plan->id,
                        'price' => $plan->price_monthly,
                        'quantity' => 1,
                        'name' => 'Paket ' . $plan->name . ' (1 Bulan)',
                    ],
                ],
                'callbacks' => [
                    'finish' => route('settings.subscription.payment.callback', ['plan' => $plan->id, 'order_id' => $orderId]),
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Get client key for Snap.js
            $clientKey = \App\Models\Setting::get('midtrans_client_key', config('midtrans.client_key'));

            // Store pending subscription in database
            SubscriptionHistory::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'amount_paid' => $plan->price_monthly,
                'payment_method' => 'midtrans',
                'status' => 'pending', // Pending payment
                'transaction_id' => $orderId,
            ]);

            return view('settings.subscription.payment', [
                'plan' => $plan,
                'snapToken' => $snapToken,
                'tenant' => $tenant,
                'clientKey' => $clientKey,
                'isProduction' => $isProduction,
            ]);

        } catch (\Exception $e) {
            \Log::error('Midtrans Error: ' . $e->getMessage());
            return redirect()->route('settings.subscription')
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment callback from Midtrans
     */
    public function paymentCallback(Request $request, \App\Models\SubscriptionPlan $plan)
    {
        $tenant = auth()->user()->tenant;
        $orderId = $request->get('order_id');
        $transactionStatus = $request->get('transaction_status', 'settlement');

        // For demo/sandbox, we'll consider 'settlement', 'capture' as success
        if (in_array($transactionStatus, ['settlement', 'capture']) || $request->has('success')) {
            // Find the pending subscription history
            $history = SubscriptionHistory::where('transaction_id', $orderId)->first();

            if ($history) {
                // Update history status
                $history->update([
                    'status' => 'active',
                    'started_at' => now(),
                    'ended_at' => now()->addMonth(),
                    'notes' => 'Pembayaran berhasil via Midtrans (Callback)',
                ]);

                // Update tenant subscription
                $tenant->current_plan_id = $plan->id;
                $tenant->subscription_ends_at = now()->addMonth();
                $tenant->status = 'active';
                $tenant->save();
            } else {
                // Should not happen if flow is correct, but fallback just in case
                SubscriptionHistory::create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'amount_paid' => $plan->price_monthly,
                    'started_at' => now(),
                    'ended_at' => now()->addMonth(),
                    'payment_method' => 'midtrans',
                    'status' => 'active',
                    'transaction_id' => $orderId,
                    'notes' => 'Created via callback fallback',
                ]);

                $tenant->current_plan_id = $plan->id;
                $tenant->subscription_ends_at = now()->addMonth();
                $tenant->status = 'active';
                $tenant->save();
            }

            session()->forget('pending_subscription');

            return redirect()->route('settings.subscription')
                ->with('success', 'Pembayaran berhasil! Paket ' . $plan->name . ' telah aktif.');
        }

        return redirect()->route('settings.subscription')
            ->with('error', 'Pembayaran belum berhasil. Silakan coba lagi.');
    }
}

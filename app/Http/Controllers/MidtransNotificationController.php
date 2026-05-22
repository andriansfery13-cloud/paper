<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionHistory;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    public function handle(Request $request)
    {
        try {
            // Configure Midtrans
            $serverKey = \App\Models\Setting::get('midtrans_server_key', config('midtrans.server_key'));

            // Determine production mode: DB setting > Config/Env > Default false
            $dbProduction = \App\Models\Setting::get('midtrans_is_production');
            if ($dbProduction !== null) {
                $isProduction = $dbProduction === 'true';
            } else {
                $isProduction = config('midtrans.is_production', false);
            }

            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = $isProduction;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $notif = new \Midtrans\Notification();

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $fraud = $notif->fraud_status;

            Log::info("Midtrans Notification: $orderId - $transaction");

            $history = SubscriptionHistory::where('transaction_id', $orderId)->first();

            if (!$history) {
                Log::error("Midtrans Notification: Order ID $orderId not found in history.");
                return response()->json(['message' => 'Order ID not found'], 404);
            }

            if ($history->status === 'active') {
                return response()->json(['message' => 'Already processed'], 200);
            }

            if ($transaction == 'capture') {
                // For credit card transaction, we need to check whether transaction is challenge by FDS or not
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        // TODO: Set payment status in merchant's database to 'Challenge'
                        // $history->update(['status' => 'challenge']);
                    } else {
                        // TODO: Set payment status in merchant's database to 'Success'
                        $this->activateSubscription($history, "Pembayaran berhasil (Capture)");
                    }
                }
            } else if ($transaction == 'settlement') {
                // TODO: set payment status in merchant's database to 'Settlement'
                $this->activateSubscription($history, "Pembayaran berhasil (Settlement)");
            } else if ($transaction == 'pending') {
                // TODO: set payment status in merchant's database to 'Pending'
                // $history->update(['status' => 'pending']);
            } else if ($transaction == 'deny') {
                // TODO: set payment status in merchant's database to 'Denied'
                $history->update(['status' => 'failed', 'notes' => 'Pembayaran ditolak']);
            } else if ($transaction == 'expire') {
                // TODO: set payment status in merchant's database to 'expire'
                $history->update(['status' => 'expired', 'notes' => 'Pembayaran kadaluarsa']);
            } else if ($transaction == 'cancel') {
                // TODO: set payment status in merchant's database to 'Denied'
                $history->update(['status' => 'cancelled', 'notes' => 'Pembayaran dibatalkan']);
            }

            return response()->json(['message' => 'Notification processed'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }

    private function activateSubscription($history, $note)
    {
        $tenant = $history->tenant;

        // Update history
        $history->update([
            'status' => 'active',
            'started_at' => now(),
            'ended_at' => now()->addMonth(), // Assuming monthly
            'notes' => $note . ' - Webhook',
            'paid_at' => now(),
        ]);

        // Update tenant
        $tenant->current_plan_id = $history->plan_id;
        $tenant->subscription_ends_at = now()->addMonth(); // Assuming monthly
        $tenant->status = 'active';
        $tenant->save();

        Log::info("Subscription Activated for Tenant {$tenant->id} via Webhook");
    }
}

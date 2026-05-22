<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Support\Str;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create Snap payment token for invoice
     */
    public function createSnapToken(Invoice $invoice)
    {
        $orderId = 'INV-' . $invoice->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $invoice->amount_due,
            ],
            'customer_details' => [
                'first_name' => $invoice->client->name,
                'email' => $invoice->client->email,
                'phone' => $invoice->client->phone,
                'billing_address' => [
                    'first_name' => $invoice->client->name,
                    'address' => $invoice->client->address,
                    'city' => $invoice->client->city,
                    'postal_code' => $invoice->client->postal_code,
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $this->formatItemDetails($invoice),
            'callbacks' => [
                'finish' => route('payment.finish', $invoice),
            ],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit' => 'days',
                'duration' => 1,
            ],
        ];

        // Store order_id for reference
        $invoice->update(['qr_payment_code' => $orderId]);

        $snapToken = Snap::getSnapToken($params);

        // Generate payment link
        $paymentLink = "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . $snapToken;
        $invoice->update(['payment_link' => $paymentLink]);

        return [
            'token' => $snapToken,
            'redirect_url' => $paymentLink,
            'order_id' => $orderId,
        ];
    }

    /**
     * Format invoice items for Midtrans
     */
    private function formatItemDetails(Invoice $invoice)
    {
        $items = [];

        foreach ($invoice->items as $item) {
            $items[] = [
                'id' => 'ITEM-' . $item->id,
                'price' => (int) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'name' => Str::limit($item->description, 50),
            ];
        }

        // Add tax if any
        if ($invoice->tax_amount > 0) {
            $items[] = [
                'id' => 'TAX',
                'price' => (int) $invoice->tax_amount,
                'quantity' => 1,
                'name' => 'PPN',
            ];
        }

        // Add discount if any (negative)
        if ($invoice->discount_amount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => (int) -$invoice->discount_amount,
                'quantity' => 1,
                'name' => 'Diskon',
            ];
        }

        // Add shipping if any
        if ($invoice->shipping_amount > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => (int) $invoice->shipping_amount,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        return $items;
    }

    /**
     * Handle notification callback from Midtrans
     */
    public function handleNotification($notification)
    {
        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $fraudStatus = $notification->fraud_status ?? null;
        $paymentType = $notification->payment_type;
        $grossAmount = $notification->gross_amount;

        // Parse invoice ID from order_id
        $parts = explode('-', $orderId);
        $invoiceId = $parts[1] ?? null;

        if (!$invoiceId) {
            return false;
        }

        $invoice = Invoice::find($invoiceId);
        if (!$invoice) {
            return false;
        }

        // Handle based on transaction status
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'accept' || $fraudStatus === null) {
                return $this->recordSuccessfulPayment($invoice, $notification);
            }
        } elseif ($transactionStatus == 'pending') {
            // Create pending payment record
            return $this->recordPendingPayment($invoice, $notification);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            return $this->recordFailedPayment($invoice, $notification);
        }

        return true;
    }

    /**
     * Record successful payment
     */
    private function recordSuccessfulPayment(Invoice $invoice, $notification)
    {
        $payment = Payment::where('gateway_transaction_id', $notification->transaction_id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'success',
                'gateway_response' => json_encode($notification),
            ]);
        } else {
            $payment = Payment::create([
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'payment_date' => now(),
                'amount' => $notification->gross_amount,
                'payment_method' => $notification->payment_type,
                'gateway_transaction_id' => $notification->transaction_id,
                'gateway_response' => json_encode($notification),
                'status' => 'success',
            ]);
        }

        return $payment;
    }

    /**
     * Record pending payment
     */
    private function recordPendingPayment(Invoice $invoice, $notification)
    {
        return Payment::updateOrCreate(
            ['gateway_transaction_id' => $notification->transaction_id],
            [
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'payment_date' => now(),
                'amount' => $notification->gross_amount,
                'payment_method' => $notification->payment_type,
                'gateway_response' => json_encode($notification),
                'status' => 'pending',
            ]
        );
    }

    /**
     * Record failed payment
     */
    private function recordFailedPayment(Invoice $invoice, $notification)
    {
        $payment = Payment::where('gateway_transaction_id', $notification->transaction_id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => json_encode($notification),
            ]);
        }

        return $payment;
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getStatus($orderId)
    {
        return Transaction::status($orderId);
    }

    /**
     * Cancel transaction
     */
    public function cancel($orderId)
    {
        return Transaction::cancel($orderId);
    }

    /**
     * Expire transaction
     */
    public function expire($orderId)
    {
        return Transaction::expire($orderId);
    }
}

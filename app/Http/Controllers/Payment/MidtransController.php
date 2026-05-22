<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\MidtransService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    protected $midtrans;
    protected $notification;

    public function __construct(MidtransService $midtrans, NotificationService $notification)
    {
        $this->midtrans = $midtrans;
        $this->notification = $notification;
    }

    /**
     * Create payment for invoice
     */
    public function createPayment(Request $request, Invoice $invoice)
    {
        // Check permission
        $this->authorize('update', $invoice);

        // Validate invoice
        if ($invoice->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice sudah lunas',
            ], 422);
        }

        if ($invoice->amount_due <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada sisa tagihan',
            ], 422);
        }

        try {
            $result = $this->midtrans->createSnapToken($invoice);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Midtrans notification callback
     */
    public function notification(Request $request)
    {
        try {
            $notification = json_decode($request->getContent());

            // Verify signature (optional but recommended)
            $serverKey = config('services.midtrans.server_key');
            $orderId = $notification->order_id;
            $statusCode = $notification->status_code;
            $grossAmount = $notification->gross_amount;

            $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signature !== $notification->signature_key) {
                return response()->json(['status' => 'invalid signature'], 403);
            }

            // Process the notification
            $payment = $this->midtrans->handleNotification($notification);

            if ($payment && $payment->status === 'success') {
                // Send notification to client
                $this->notification->notifyPaymentReceived($payment);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            \Log::error('Midtrans notification error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Handle finish callback (redirect from payment page)
     */
    public function finish(Request $request, Invoice $invoice)
    {
        $orderId = $request->get('order_id');
        $statusCode = $request->get('status_code');
        $transactionStatus = $request->get('transaction_status');

        if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Pembayaran berhasil! Terima kasih.');
        } elseif ($transactionStatus === 'pending') {
            return redirect()->route('invoices.show', $invoice)
                ->with('warning', 'Pembayaran pending. Silakan selesaikan pembayaran Anda.');
        } else {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Pembayaran dibatalkan atau gagal.');
        }
    }

    /**
     * Handle unfinish callback
     */
    public function unfinish(Request $request, Invoice $invoice)
    {
        return redirect()->route('invoices.show', $invoice)
            ->with('warning', 'Pembayaran belum selesai. Silakan coba lagi.');
    }

    /**
     * Handle error callback
     */
    public function error(Request $request, Invoice $invoice)
    {
        return redirect()->route('invoices.show', $invoice)
            ->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request, Invoice $invoice)
    {
        if (!$invoice->qr_payment_code) {
            return response()->json([
                'success' => false,
                'message' => 'No payment initiated',
            ], 404);
        }

        try {
            $status = $this->midtrans->getStatus($invoice->qr_payment_code);

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

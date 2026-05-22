<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send WhatsApp message via Fonnte API
     */
    public function sendWhatsApp($tenant, string $phone, string $message, ?string $attachment = null): bool
    {
        $token = null;

        // 1. Check Tenant Settings
        if ($tenant && isset($tenant->settings['notifications']['whatsapp']['enabled']) && $tenant->settings['notifications']['whatsapp']['enabled']) {
            $token = $tenant->settings['notifications']['whatsapp']['token'] ?? null;
        }

        // 2. Check Global Settings if no tenant token
        if (empty($token)) {
            $enabled = \App\Models\Setting::get('whatsapp_enabled', 'false') === 'true';
            if ($enabled) {
                $token = \App\Models\Setting::get('whatsapp_token');
            }
        }

        if (empty($token)) {
            Log::warning("WhatsApp token not configured (Tenant or Global)");
            return false;
        }

        $apiUrl = config('services.whatsapp.api_url');

        // Format phone number for Indonesia (+62)
        $phone = $this->formatPhoneNumber($phone);

        try {
            $payload = [
                'target' => $phone,
                'message' => $message,
            ];

            if ($attachment) {
                $payload['url'] = $attachment;
            }

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post($apiUrl, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp sent to {$phone}", $response->json());
                return true;
            }

            Log::error("WhatsApp failed to {$phone}", $response->json());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Telegram message
     */
    public function sendTelegram($tenant, string $chatId, string $message, ?string $parseMode = 'HTML'): bool
    {
        // Get global settings
        $enabled = \App\Models\Setting::get('telegram_enabled', 'false') === 'true';

        if (!$enabled) {
            return false;
        }

        $token = \App\Models\Setting::get('telegram_bot_token');
        $apiUrl = config('services.telegram.api_url');

        if (empty($token)) {
            Log::warning("Telegram bot token not configured (Global)");
            return false;
        }

        try {
            $response = Http::post("{$apiUrl}{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
            ]);

            if ($response->successful()) {
                Log::info("Telegram sent to {$chatId}");
                return true;
            }

            Log::error("Telegram failed to {$chatId}", $response->json());
            return false;
        } catch (\Exception $e) {
            Log::error("Telegram error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Telegram document
     */
    public function sendTelegramDocument($tenant, string $chatId, string $documentPath, ?string $caption = null): bool
    {
        // Get global settings
        $enabled = \App\Models\Setting::get('telegram_enabled', 'false') === 'true';

        if (!$enabled) {
            return false;
        }

        $token = \App\Models\Setting::get('telegram_bot_token');
        $apiUrl = config('services.telegram.api_url');

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::attach(
                'document',
                file_get_contents($documentPath),
                basename($documentPath)
            )->post("{$apiUrl}{$token}/sendDocument", [
                        'chat_id' => $chatId,
                        'caption' => $caption,
                    ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram document error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format Indonesian phone number
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert 08xx to 628xx
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Add 62 if not present
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Send invoice notification to client
     */
    public function notifyInvoiceCreated($invoice): void
    {
        if (!$invoice->client)
            return;

        $tenant = $invoice->tenant;

        $message = "🧾 *Invoice Baru*\n\n";
        $message .= "No: {$invoice->invoice_number}\n";
        $message .= "Dari: {$invoice->tenant->company_name}\n";
        $message .= "Total: Rp " . number_format($invoice->total, 0, ',', '.') . "\n";
        $message .= "Jatuh Tempo: {$invoice->due_date->format('d M Y')}\n\n";

        if ($invoice->payment_link) {
            $message .= "🔗 Bayar Online: {$invoice->payment_link}\n";
        }

        $message .= "📄 Unduh Invoice: {$invoice->verification_url}";

        if ($invoice->client->phone) {
            $this->sendWhatsApp($tenant, $invoice->client->phone, $message);
        }

        if ($invoice->client->telegram_chat_id) {
            $this->sendTelegram($tenant, $invoice->client->telegram_chat_id, $message);
        }

        // Notify Admin if configured
        $adminChatId = $tenant->settings['notifications']['telegram']['chat_id'] ?? null;
        if ($adminChatId) {
            $adminMessage = "🔔 *Info: Invoice Dibuat*\nClient: {$invoice->client->name}\nAmount: " . number_format($invoice->total);
            $this->sendTelegram($tenant, $adminChatId, $adminMessage);
        }
    }

    /**
     * Send payment confirmation notification
     */
    public function notifyPaymentReceived($payment): void
    {
        $invoice = $payment->invoice;
        if (!$invoice || !$invoice->client)
            return;

        $tenant = $invoice->tenant;

        $message = "✅ *Pembayaran Diterima*\n\n";
        $message .= "Invoice: {$invoice->invoice_number}\n";
        $message .= "Jumlah: Rp " . number_format($payment->amount, 0, ',', '.') . "\n";
        $message .= "Metode: {$payment->payment_method}\n";
        $message .= "Tanggal: {$payment->payment_date->format('d M Y H:i')}\n\n";

        if ($invoice->amount_due > 0) {
            $message .= "Sisa Tagihan: Rp " . number_format($invoice->amount_due, 0, ',', '.') . "";
        } else {
            $message .= "Status: LUNAS ✅";
        }

        if ($invoice->client->phone) {
            $this->sendWhatsApp($tenant, $invoice->client->phone, $message);
        }

        if ($invoice->client->telegram_chat_id) {
            $this->sendTelegram($tenant, $invoice->client->telegram_chat_id, $message);
        }
    }

    /**
     * Send overdue invoice reminder
     */
    public function notifyInvoiceOverdue($invoice): void
    {
        if (!$invoice->client)
            return;

        $tenant = $invoice->tenant;

        $message = "⚠️ *Reminder: Invoice Jatuh Tempo*\n\n";
        $message .= "No: {$invoice->invoice_number}\n";
        $message .= "Sisa Tagihan: Rp " . number_format($invoice->amount_due, 0, ',', '.') . "\n";
        $message .= "Jatuh Tempo: {$invoice->due_date->format('d M Y')}\n\n";
        $message .= "Mohon segera melakukan pembayaran.";

        if ($invoice->payment_link) {
            $message .= "\n\n🔗 Bayar: {$invoice->payment_link}";
        }

        if ($invoice->client->phone) {
            $this->sendWhatsApp($tenant, $invoice->client->phone, $message);
        }
    }

    /**
     * Send quotation notification
     */
    public function notifyQuotationCreated($quotation): void
    {
        if (!$quotation->client)
            return;

        $tenant = $quotation->tenant;

        $message = "📋 *Quotation Baru*\n\n";
        $message .= "No: {$quotation->quotation_number}\n";
        $message .= "Dari: {$quotation->tenant->company_name}\n";
        $message .= "Total: Rp " . number_format($quotation->total, 0, ',', '.') . "\n";
        $message .= "Berlaku Sampai: {$quotation->valid_until->format('d M Y')}\n\n";
        $message .= "Silakan review dan konfirmasi quotation ini.\n\n";

        // Add download link
        if ($quotation->verification_code) {
            $message .= "📄 Lihat/Unduh Quotation: {$quotation->verification_url}";
        } else {
            $downloadUrl = route('quotations.preview', $quotation->id);
            $message .= "📄 Lihat Quotation: {$downloadUrl}";
        }

        if ($quotation->client->phone) {
            $this->sendWhatsApp($tenant, $quotation->client->phone, $message);
        }
    }
    /**
     * Send delivery note notification
     */
    public function notifyDeliveryNoteCreated($deliveryNote): void
    {
        $invoice = $deliveryNote->invoice;
        if (!$invoice || !$invoice->client)
            return;

        $tenant = $deliveryNote->tenant;
        $client = $invoice->client;

        $message = "🚚 *Surat Jalan Baru*\n\n";
        $message .= "No: {$deliveryNote->delivery_number}\n";
        $message .= "Ref Invoice: {$invoice->invoice_number}\n";
        $message .= "Dari: {$tenant->company_name}\n";
        $message .= "Tanggal: {$deliveryNote->delivery_date->format('d M Y')}\n\n";
        $message .= "Status Pengiriman: " . ucfirst($deliveryNote->status) . "\n\n";

        // Add download link
        $downloadUrl = route('delivery-notes.preview', $deliveryNote->id);
        $message .= "📄 Lihat/Unduh Surat Jalan: {$downloadUrl}";

        if ($client->phone) {
            $this->sendWhatsApp($tenant, $client->phone, $message);
        }
    }

    /**
     * Send receipt notification
     */
    public function notifyReceiptCreated($receipt): void
    {
        $invoice = $receipt->invoice;
        if (!$invoice || !$invoice->client)
            return;

        $tenant = $receipt->tenant;
        $client = $invoice->client;

        $message = "🧾 *Kwitansi Pembayaran*\n\n";
        $message .= "No: {$receipt->receipt_number}\n";
        $message .= "Ref Invoice: {$invoice->invoice_number}\n";
        $message .= "Diterima Dari: {$client->name}\n";
        $message .= "Jumlah: Rp " . number_format($receipt->amount, 0, ',', '.') . "\n";
        $message .= "Tanggal: {$receipt->receipt_date->format('d M Y')}\n";
        $message .= "Untuk: Pembayaran Invoice #{$invoice->invoice_number}\n\n";

        // Add download link
        $downloadUrl = route('receipts.preview', $receipt->id);
        $message .= "📄 Lihat/Unduh Kwitansi: {$downloadUrl}";

        if ($client->phone) {
            $this->sendWhatsApp($tenant, $client->phone, $message);
        }
    }
}

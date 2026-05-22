<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
        $this->middleware('superadmin')->only(['notifications', 'updateNotifications']);
    }

    /**
     * Show company settings form
     */
    public function company()
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke pengaturan perusahaan. Pastikan akun Anda terhubung dengan tenant.');
        }

        return view('settings.company', compact('tenant'));
    }

    /**
     * Update company settings
     */
    public function updateCompany(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'npwp' => 'nullable|string|max:30',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stamp_image' => 'nullable|image|mimes:png|max:1024',
            'signature_image' => 'nullable|image|mimes:png|max:1024',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $validated['logo'] = $request->file('logo')->store('tenants/logos', 'public');
        }

        // Handle stamp upload
        if ($request->hasFile('stamp_image')) {
            if ($tenant->stamp_image) {
                Storage::disk('public')->delete($tenant->stamp_image);
            }
            $validated['stamp_image'] = $request->file('stamp_image')->store('tenants/stamps', 'public');
        }

        // Handle signature upload
        if ($request->hasFile('signature_image')) {
            if ($tenant->signature_image) {
                Storage::disk('public')->delete($tenant->signature_image);
            }
            $validated['signature_image'] = $request->file('signature_image')->store('tenants/signatures', 'public');
        }

        // Check if email or phone changed, reset verification
        if ($request->has('email') && $request->email !== $tenant->email) {
            $validated['email_verified_at'] = null;
        }
        if ($request->has('phone') && $request->phone !== $tenant->phone) {
            $validated['phone_verified_at'] = null;
        }

        $tenant->update($validated);

        return back()->with('success', 'Pengaturan perusahaan berhasil disimpan');
    }

    public function sendOtp(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $type = $request->input('type'); // 'email' or 'phone'

        if ($type === 'email') {
            $otp = rand(100000, 999999);
            $tenant->update([
                'email_otp' => $otp,
                'email_otp_expires_at' => now()->addMinutes(10),
            ]);

            // Send Email
            try {
                \Illuminate\Support\Facades\Mail::raw("Kode OTP verifikasi email Anda adalah: $otp", function ($message) use ($tenant) {
                    $message->to($tenant->email)
                        ->subject('Kode OTP Verifikasi Email')
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
                return response()->json(['success' => true, 'message' => 'OTP sent to email']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
            }

        } elseif ($type === 'phone') {
            if (!$tenant->phone) {
                return response()->json(['success' => false, 'message' => 'Phone number is empty'], 400);
            }
            $otp = rand(100000, 999999);
            $tenant->update([
                'phone_otp' => $otp,
                'phone_otp_expires_at' => now()->addMinutes(10),
            ]);

            // Send WA
            $notificationService = new \App\Services\NotificationService();
            $sent = $notificationService->sendWhatsApp($tenant, $tenant->phone, "Kode OTP verifikasi nomor HP Anda adalah: $otp");

            if ($sent) {
                return response()->json(['success' => true, 'message' => 'OTP sent to WhatsApp']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to send WhatsApp. Pastikan gateway aktif.'], 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
    }

    public function verifyOtp(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $type = $request->input('type');
        $otp = $request->input('otp');

        if ($type === 'email') {
            if ($tenant->email_otp == $otp && $tenant->email_otp_expires_at->isFuture()) {
                $tenant->update([
                    'email_verified_at' => now(),
                    'email_otp' => null,
                    'email_otp_expires_at' => null,
                ]);
                return response()->json(['success' => true, 'message' => 'Email berhasil diverifikasi']);
            }
        } elseif ($type === 'phone') {
            if ($tenant->phone_otp == $otp && $tenant->phone_otp_expires_at->isFuture()) {
                $tenant->update([
                    'phone_verified_at' => now(),
                    'phone_otp' => null,
                    'phone_otp_expires_at' => null,
                ]);
                return response()->json(['success' => true, 'message' => 'Nomor HP berhasil diverifikasi']);
            }
        }

        return response()->json(['success' => false, 'message' => 'OTP salah atau kadaluarsa'], 400);
    }

    /**
     * Show invoice settings form
     */
    public function invoice()
    {
        $tenant = auth()->user()->tenant;
        return view('settings.invoice', compact('tenant'));
    }

    /**
     * Update invoice settings
     */
    public function updateInvoice(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'invoice_prefix' => 'nullable|string|max:10',
            'quotation_prefix' => 'nullable|string|max:10',
            'receipt_prefix' => 'nullable|string|max:10',
            'delivery_prefix' => 'nullable|string|max:10',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'default_payment_terms' => 'nullable|integer|min:0|max:365',
            'default_notes' => 'nullable|string|max:2000',
            'default_terms' => 'nullable|string|max:2000',
        ]);

        // Get existing invoice_settings or empty array
        $invoiceSettings = $tenant->invoice_settings ?? [];

        // Merge new settings
        $invoiceSettings['default_tax_rate'] = $validated['default_tax_rate'] ?? 11;
        $invoiceSettings['default_payment_terms'] = $validated['default_payment_terms'] ?? 30;
        $invoiceSettings['default_notes'] = $validated['default_notes'] ?? '';
        $invoiceSettings['default_terms'] = $validated['default_terms'] ?? '';

        $tenant->update([
            'invoice_prefix' => $validated['invoice_prefix'] ?? 'INV',
            'quotation_prefix' => $validated['quotation_prefix'] ?? 'QUO',
            'receipt_prefix' => $validated['receipt_prefix'] ?? 'RCP',
            'delivery_prefix' => $validated['delivery_prefix'] ?? 'DO',
            'invoice_settings' => $invoiceSettings,
        ]);

        return back()->with('success', 'Pengaturan invoice berhasil disimpan');
    }

    /**
     * Show email settings form
     */
    public function email()
    {
        $tenant = auth()->user()->tenant;
        return view('settings.email', compact('tenant'));
    }

    /**
     * Update email settings
     */
    public function updateEmail(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'email_notifications_enabled' => 'boolean',
            'email_from_name' => 'nullable|string|max:100',
            'email_cc' => 'nullable|email|max:255',
            'email_bcc' => 'nullable|email|max:255',
            'email_footer' => 'nullable|string|max:500',
            'quotation_email_subject' => 'nullable|string|max:200',
            'invoice_email_subject' => 'nullable|string|max:200',
            'payment_email_subject' => 'nullable|string|max:200',
        ]);

        // Get existing settings or empty array
        $settings = $tenant->settings ?? [];

        // Merge email settings
        $settings['email'] = [
            'notifications_enabled' => $validated['email_notifications_enabled'] ?? false,
            'from_name' => $validated['email_from_name'] ?? $tenant->company_name,
            'cc' => $validated['email_cc'] ?? null,
            'bcc' => $validated['email_bcc'] ?? null,
            'footer' => $validated['email_footer'] ?? '',
            'quotation_subject' => $validated['quotation_email_subject'] ?? 'Penawaran dari {company_name}',
            'invoice_subject' => $validated['invoice_email_subject'] ?? 'Invoice dari {company_name}',
            'payment_subject' => $validated['payment_email_subject'] ?? 'Konfirmasi Pembayaran',
        ];

        $tenant->update(['settings' => $settings]);

        return back()->with('success', 'Pengaturan email berhasil disimpan');
    }

    /**
     * Show notification settings form
     */
    public function notifications()
    {
        $tenant = auth()->user()->tenant;
        return view('settings.notifications', compact('tenant'));
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'whatsapp_enabled' => 'boolean',
            'whatsapp_token' => 'nullable|string|max:255',
            'telegram_enabled' => 'boolean',
            'telegram_bot_token' => 'nullable|string|max:255',
            'telegram_chat_id' => 'nullable|string|max:255',
        ]);

        // Get existing settings or empty array
        $settings = $tenant->settings ?? [];

        // Merge notification settings
        $settings['notifications'] = [
            'whatsapp' => [
                'enabled' => $validated['whatsapp_enabled'] ?? false,
                'token' => $validated['whatsapp_token'] ?? null,
            ],
            'telegram' => [
                'enabled' => $validated['telegram_enabled'] ?? false,
                'bot_token' => $validated['telegram_bot_token'] ?? null,
                'chat_id' => $validated['telegram_chat_id'] ?? null,
            ],
        ];

        $tenant->update(['settings' => $settings]);

        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan');
    }
}

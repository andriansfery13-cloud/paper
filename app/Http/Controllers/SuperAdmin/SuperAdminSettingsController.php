<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SuperAdminSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'superadmin']);
    }

    public function smtp()
    {
        $settings = [
            'mail_mailer' => Setting::get('mail_mailer', 'smtp'),
            'mail_host' => Setting::get('mail_host'),
            'mail_port' => Setting::get('mail_port', '587'),
            'mail_username' => Setting::get('mail_username'),
            'mail_password' => Setting::get('mail_password'),
            'mail_encryption' => Setting::get('mail_encryption', 'tls'),
            'mail_from_address' => Setting::get('mail_from_address'),
            'mail_from_name' => Setting::get('mail_from_name'),
        ];

        return view('superadmin.settings.smtp', compact('settings'));
    }

    public function updateSmtp(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        Setting::set('mail_mailer', 'smtp', 'smtp');
        Setting::set('mail_host', $request->mail_host, 'smtp');
        Setting::set('mail_port', $request->mail_port, 'smtp');
        Setting::set('mail_username', $request->mail_username, 'smtp');

        if ($request->filled('mail_password')) {
            Setting::set('mail_password', $request->mail_password, 'smtp');
        }

        Setting::set('mail_encryption', $request->mail_encryption, 'smtp');
        Setting::set('mail_from_address', $request->mail_from_address, 'smtp');
        Setting::set('mail_from_name', $request->mail_from_name, 'smtp');

        return back()->with('success', 'Pengaturan SMTP berhasil disimpan. Konfigurasi ini akan digunakan untuk semua email sistem.');
    }
}

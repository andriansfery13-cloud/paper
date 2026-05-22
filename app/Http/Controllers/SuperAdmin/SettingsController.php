<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'superadmin']);
    }

    /**
     * Show system settings page
     */
    public function index()
    {
        $settings = [
            'midtrans_server_key' => $this->getSetting('midtrans_server_key', ''),
            'midtrans_client_key' => $this->getSetting('midtrans_client_key', ''),
            'midtrans_is_production' => $this->getSetting('midtrans_is_production', 'false'),
            'app_name' => $this->getSetting('app_name', config('app.name')),
            'app_url' => $this->getSetting('app_url', config('app.url')),
        ];

        return view('superadmin.settings.index', compact('settings'));
    }

    /**
     * Update Midtrans settings
     */
    public function updateMidtrans(Request $request)
    {
        $request->validate([
            'midtrans_server_key' => 'required|string',
            'midtrans_client_key' => 'required|string',
            'midtrans_is_production' => 'required|in:true,false',
        ]);

        $this->setSetting('midtrans_server_key', $request->midtrans_server_key);
        $this->setSetting('midtrans_client_key', $request->midtrans_client_key);
        $this->setSetting('midtrans_is_production', $request->midtrans_is_production);

        // Clear config cache
        Artisan::call('config:clear');

        return back()->with('success', 'Pengaturan Midtrans berhasil disimpan.');
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
        ]);

        $this->setSetting('app_name', $request->app_name);
        $this->setSetting('app_url', $request->app_url);

        return back()->with('success', 'Pengaturan umum berhasil disimpan.');
    }

    /**
     * Show notification settings form
     */
    public function notifications()
    {
        $settings = [
            'whatsapp_enabled' => $this->getSetting('whatsapp_enabled', 'false'),
            'whatsapp_token' => $this->getSetting('whatsapp_token', ''),
            'telegram_enabled' => $this->getSetting('telegram_enabled', 'false'),
            'telegram_bot_token' => $this->getSetting('telegram_bot_token', ''),
            'telegram_chat_id' => $this->getSetting('telegram_chat_id', ''),
        ];

        return view('superadmin.settings.notifications', compact('settings'));
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'whatsapp_enabled' => 'nullable|in:1,0',
            'whatsapp_token' => 'nullable|string',
            'telegram_enabled' => 'nullable|in:1,0',
            'telegram_bot_token' => 'nullable|string',
            'telegram_chat_id' => 'nullable|string',
        ]);

        $this->setSetting('whatsapp_enabled', $request->has('whatsapp_enabled') ? 'true' : 'false');
        $this->setSetting('whatsapp_token', $request->whatsapp_token);
        $this->setSetting('telegram_enabled', $request->has('telegram_enabled') ? 'true' : 'false');
        $this->setSetting('telegram_bot_token', $request->telegram_bot_token);
        $this->setSetting('telegram_chat_id', $request->telegram_chat_id);

        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }

    /**
     * Get setting value
     */
    protected function getSetting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value
     */
    protected function setSetting($key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}

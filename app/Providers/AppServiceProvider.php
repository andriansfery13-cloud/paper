<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $host = \App\Models\Setting::get('mail_host');

                // Only override if host is set in DB
                if (!empty($host)) {
                    config([
                        'mail.mailers.smtp.host' => $host,
                        'mail.mailers.smtp.port' => \App\Models\Setting::get('mail_port', 587),
                        'mail.mailers.smtp.username' => \App\Models\Setting::get('mail_username'),
                        'mail.mailers.smtp.password' => \App\Models\Setting::get('mail_password'),
                        'mail.mailers.smtp.encryption' => \App\Models\Setting::get('mail_encryption'),
                        'mail.from.address' => \App\Models\Setting::get('mail_from_address'),
                        'mail.from.name' => \App\Models\Setting::get('mail_from_name'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Ignore if DB connection fails or during migration
        }
    }
}

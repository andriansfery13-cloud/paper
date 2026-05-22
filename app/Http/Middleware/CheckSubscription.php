<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    /**
     * Check if tenant has valid subscription
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Skip for super admin
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has tenant
        if (!$user->tenant_id) {
            return redirect()->route('login')->with('error', 'Akun tidak terhubung dengan perusahaan.');
        }

        $tenant = $user->tenant;

        // Check subscription validity
        if (!$tenant->isSubscriptionValid()) {
            // Allow access to subscription pages only
            if ($request->routeIs('subscription.*') || $request->routeIs('dashboard')) {
                session()->flash('warning', 'Langganan Anda telah berakhir. Silakan perpanjang untuk menggunakan semua fitur.');
                return $next($request);
            }

            return redirect()->route('dashboard')->with('error', 'Langganan Anda telah berakhir. Silakan perpanjang untuk menggunakan fitur ini.');
        }

        // Check if tenant is active
        if (!$tenant->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun perusahaan Anda telah dinonaktifkan.');
        }

        return $next($request);
    }
}

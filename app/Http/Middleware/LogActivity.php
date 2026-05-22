<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    /**
     * HTTP methods to log
     */
    protected $loggableMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Routes to exclude from logging
     */
    protected $excludeRoutes = [
        'login',
        'logout',
        'register',
        'password.*',
        'sanctum.*',
        'livewire.*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log for authenticated users
        if (!Auth::check()) {
            return $response;
        }

        // Only log specific HTTP methods
        if (!in_array($request->method(), $this->loggableMethods)) {
            return $response;
        }

        // Skip excluded routes
        if ($this->shouldExclude($request)) {
            return $response;
        }

        // Log the activity
        $this->logActivity($request, $response);

        return $response;
    }

    /**
     * Check if route should be excluded from logging
     */
    protected function shouldExclude(Request $request): bool
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        if (!$routeName) {
            return false;
        }

        foreach ($this->excludeRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log the activity
     */
    protected function logActivity(Request $request, $response): void
    {
        $user = Auth::user();
        $route = $request->route();
        $routeName = $route ? $route->getName() : 'unknown';
        $action = $this->determineAction($request);
        $module = $this->determineModule($routeName);

        // Don't log if response is not successful for create/update operations
        if ($response->getStatusCode() >= 400) {
            return;
        }

        try {
            ActivityLog::create([
                'tenant_id' => $user->tenant_id,
                'user_id' => $user->id,
                'action' => $action,
                'module' => $module,
                'description' => $this->generateDescription($action, $module, $request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'new_values' => $this->sanitizeData($request->except([
                    '_token',
                    '_method',
                    'password',
                    'password_confirmation',
                ])),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't disrupt the main request
            \Log::error('Activity logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine action based on HTTP method
     */
    protected function determineAction(Request $request): string
    {
        $method = $request->method();
        $route = $request->route();
        $routeName = $route ? ($route->getName() ?? '') : '';

        // Check for specific action keywords in route name
        if (str_contains($routeName, '.store') || $method === 'POST') {
            if (str_contains($routeName, 'send')) {
                return 'sent';
            }
            if (str_contains($routeName, 'approve')) {
                return 'approved';
            }
            if (str_contains($routeName, 'reject')) {
                return 'rejected';
            }
            if (str_contains($routeName, 'suspend')) {
                return 'suspended';
            }
            if (str_contains($routeName, 'activate')) {
                return 'activated';
            }
            return 'created';
        }

        if (str_contains($routeName, '.update') || in_array($method, ['PUT', 'PATCH'])) {
            return 'updated';
        }

        if (str_contains($routeName, '.destroy') || $method === 'DELETE') {
            return 'deleted';
        }

        return 'action';
    }

    /**
     * Determine module from route name
     */
    protected function determineModule(string $routeName): string
    {
        $parts = explode('.', $routeName);

        if (count($parts) >= 2) {
            // Handle resource routes like 'invoices.store', 'clients.update'
            return $parts[0];
        }

        return $routeName ?: 'unknown';
    }

    /**
     * Generate human-readable description
     */
    protected function generateDescription(string $action, string $module, Request $request): string
    {
        $actionLabels = [
            'created' => 'membuat',
            'updated' => 'mengupdate',
            'deleted' => 'menghapus',
            'sent' => 'mengirim',
            'approved' => 'menyetujui',
            'rejected' => 'menolak',
            'suspended' => 'menonaktifkan',
            'activated' => 'mengaktifkan',
        ];

        $moduleLabels = [
            'invoices' => 'Invoice',
            'quotations' => 'Penawaran',
            'clients' => 'Client',
            'products' => 'Produk',
            'payments' => 'Pembayaran',
            'receipts' => 'Kwitansi',
            'delivery-notes' => 'Surat Jalan',
            'expenses' => 'Pengeluaran',
            'income' => 'Pemasukan',
            'settings' => 'Pengaturan',
            'templates' => 'Template',
        ];

        $actionLabel = $actionLabels[$action] ?? $action;
        $moduleLabel = $moduleLabels[$module] ?? ucfirst($module);

        return ucfirst($actionLabel) . ' ' . $moduleLabel;
    }

    /**
     * Sanitize data to remove sensitive information
     */
    protected function sanitizeData(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'secret',
            'token',
            'api_key',
            'credit_card',
            'cvv',
        ];

        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $data[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeData($value);
            }
        }

        return $data;
    }
}

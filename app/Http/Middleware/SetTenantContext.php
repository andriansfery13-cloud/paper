<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetTenantContext
{
    /**
     * Set the tenant context for the current request
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->tenant_id) {
            // Store tenant in request for easy access
            $request->merge(['tenant' => auth()->user()->tenant]);

            // Share with views
            view()->share('currentTenant', auth()->user()->tenant);
        }

        return $next($request);
    }
}

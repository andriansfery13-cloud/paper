<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])
        ->middleware('throttle:5,1'); // 5 attempts per minute
});

// Protected API routes
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Authentication
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', [App\Http\Controllers\Api\AuthController::class, 'user']);

    // Clients
    Route::apiResource('clients', App\Http\Controllers\Api\ClientController::class);

    // Invoices
    Route::apiResource('invoices', App\Http\Controllers\Api\InvoiceController::class);
    Route::post('/invoices/{invoice}/send', [App\Http\Controllers\Api\InvoiceController::class, 'send']);

    // Products
    Route::get('/products', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => \App\Models\Product::where('tenant_id', $request->user()->tenant_id)
                ->active()
                ->orderBy('name')
                ->paginate($request->get('per_page', 15)),
        ]);
    });

    // Payments
    Route::get('/payments', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => \App\Models\Payment::whereHas('invoice', function ($q) use ($request) {
                $q->where('tenant_id', $request->user()->tenant_id);
            })->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15)),
        ]);
    });
});

// API Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Midtrans Webhook
Route::post('/midtrans/notification', [App\Http\Controllers\MidtransNotificationController::class, 'handle']);

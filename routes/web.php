<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\ClientController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Transaction\InvoiceController;
use App\Http\Controllers\Transaction\QuotationController;
use App\Http\Controllers\Transaction\PaymentController;
use App\Http\Controllers\Payment\MidtransController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\DocumentTemplateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isSuperAdmin()
            ? redirect()->route('superadmin.dashboard')
            : redirect()->route('dashboard');
    }

    $plans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('price_monthly', 'asc')->get();

    return view('welcome', compact('plans'));
})->name('home');

// Document Verification (Public)
// Document Verification (Public)
Route::get('/verify/invoice/{code}', [App\Http\Controllers\PublicDocumentController::class, 'verifyInvoice'])->name('verify.invoice');
Route::get('/verify/invoice/{code}/pdf', [App\Http\Controllers\PublicDocumentController::class, 'downloadInvoicePdf'])->name('verify.invoice.pdf');

Route::get('/verify/quotation/{code}', [App\Http\Controllers\PublicDocumentController::class, 'verifyQuotation'])->name('verify.quotation');
Route::get('/verify/quotation/{code}/pdf', [App\Http\Controllers\PublicDocumentController::class, 'downloadQuotationPdf'])->name('verify.quotation.pdf');

Route::get('/verify/receipt/{code}', [App\Http\Controllers\PublicDocumentController::class, 'verifyReceipt'])->name('verify.receipt');
Route::get('/verify/receipt/{code}/pdf', [App\Http\Controllers\PublicDocumentController::class, 'downloadReceiptPdf'])->name('verify.receipt.pdf');

Route::get('/verify/delivery-note/{code}', [App\Http\Controllers\PublicDocumentController::class, 'verifyDeliveryNote'])->name('verify.delivery-note');
Route::get('/verify/delivery-note/{code}/pdf', [App\Http\Controllers\PublicDocumentController::class, 'downloadDeliveryNotePdf'])->name('verify.delivery-note.pdf');
// Old route kept for backward compatibility if needed, or update previous usages
// Route::get('/verify/{code}', [VerificationController::class, 'show'])->name('verify');

// Midtrans Callback (Public - no auth)
Route::post('/payment/notification', [MidtransController::class, 'notification'])->name('payment.notification');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // OTP Verification
    Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('verify.otp.form');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend.otp');

    // Email Verification (Registration)
    Route::get('/registration-success', function () {
        return view('auth.registration-success');
    })->name('registration.success');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed'])
        ->name('verification.verify');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Subscription Payment Routes
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/pricing', [App\Http\Controllers\MidtransController::class, 'pricing'])->name('pricing');
        Route::post('/checkout', [App\Http\Controllers\MidtransController::class, 'checkout'])->name('checkout');
        Route::get('/finish', [App\Http\Controllers\MidtransController::class, 'finish'])->name('finish');
        Route::get('/quota-status', [App\Http\Controllers\MidtransController::class, 'quotaStatus'])->name('quota-status');
    });
});

// Midtrans Callback (No Auth)
Route::post('/midtrans/callback', [App\Http\Controllers\MidtransController::class, 'callback'])->name('midtrans.callback');

// Tenant Routes (Authenticated + Tenant Context + Subscription Check)
Route::middleware(['auth', 'tenant', 'subscription'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart');

    // Master Data - Clients
    Route::resource('clients', ClientController::class);
    Route::get('/api/clients/select', [ClientController::class, 'select'])->name('clients.select');

    // Master Data - Products
    Route::resource('products', ProductController::class);
    Route::get('/api/products/select', [ProductController::class, 'select'])->name('products.select');

    // Transactions - Quotations
    Route::resource('quotations', QuotationController::class);
    Route::post('/quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
    Route::post('/quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
    Route::post('/quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convertToInvoice'])->name('quotations.convert');
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
    Route::get('/quotations/{quotation}/preview', [QuotationController::class, 'preview'])->name('quotations.preview');

    // Transactions - Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('invoices/{invoice}/send-auto', [InvoiceController::class, 'sendAuto'])->name('invoices.send_auto');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('/invoices/{invoice}/preview', [InvoiceController::class, 'preview'])->name('invoices.preview');
    Route::post('/invoices/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoices.duplicate');

    // Auto Send Routes
    Route::post('quotations/{quotation}/send-auto', [QuotationController::class, 'sendAuto'])->name('quotations.send_auto');
    Route::post('delivery-notes/{delivery_note}/send-auto', [App\Http\Controllers\Transaction\DeliveryNoteController::class, 'sendAuto'])->name('delivery-notes.send_auto');
    Route::post('receipts/{receipt}/send-auto', [App\Http\Controllers\Transaction\ReceiptController::class, 'sendAuto'])->name('receipts.send_auto');

    // Transactions - Payments
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show']);

    // Payment Gateway (Midtrans)
    Route::post('/invoices/{invoice}/pay', [MidtransController::class, 'createPayment'])->name('invoices.pay');
    Route::get('/invoices/{invoice}/payment/finish', [MidtransController::class, 'finish'])->name('payment.finish');
    Route::get('/invoices/{invoice}/payment/unfinish', [MidtransController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/invoices/{invoice}/payment/error', [MidtransController::class, 'error'])->name('payment.error');
    Route::get('/invoices/{invoice}/payment/status', [MidtransController::class, 'checkStatus'])->name('payment.status');

    // Excel Exports
    Route::get('/export/invoices', [ExportController::class, 'invoices'])->name('export.invoices');
    Route::get('/export/clients', [ExportController::class, 'clients'])->name('export.clients');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/company', [App\Http\Controllers\SettingsController::class, 'company'])->name('company');
        Route::post('/company', [App\Http\Controllers\SettingsController::class, 'updateCompany'])->name('company.update');
        Route::post('/company/send-otp', [App\Http\Controllers\SettingsController::class, 'sendOtp'])->name('company.send-otp');
        Route::post('/company/verify-otp', [App\Http\Controllers\SettingsController::class, 'verifyOtp'])->name('company.verify-otp');
        Route::get('/invoice', [App\Http\Controllers\SettingsController::class, 'invoice'])->name('invoice');
        Route::post('/invoice', [App\Http\Controllers\SettingsController::class, 'updateInvoice'])->name('invoice.update');
        Route::get('/email', [App\Http\Controllers\SettingsController::class, 'email'])->name('email');
        Route::post('/email', [App\Http\Controllers\SettingsController::class, 'updateEmail'])->name('email.update');

        // Document Templates
        Route::get('/templates/preview/{id}', [DocumentTemplateController::class, 'preview'])->name('templates.preview');
        Route::post('/templates/{template}/use', [DocumentTemplateController::class, 'use'])->name('templates.use');
        Route::resource('/templates', DocumentTemplateController::class);

        // Subscription
        Route::get('/subscription', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription');
        Route::get('/subscription/history', [App\Http\Controllers\SubscriptionController::class, 'history'])->name('subscription.history');
        Route::post('/subscription/purchase/{plan}', [App\Http\Controllers\SubscriptionController::class, 'purchase'])->name('subscription.purchase');
        Route::get('/subscription/payment/callback/{plan}', [App\Http\Controllers\SubscriptionController::class, 'paymentCallback'])->name('subscription.payment.callback');

        // User Management
        Route::resource('users', App\Http\Controllers\UserController::class)->except(['show']);
    });

    // Transactions - Receipts (Kwitansi)
    Route::resource('receipts', App\Http\Controllers\Transaction\ReceiptController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('/receipts/{receipt}/pdf', [App\Http\Controllers\Transaction\ReceiptController::class, 'pdf'])->name('receipts.pdf');
    Route::get('/receipts/{receipt}/preview', [App\Http\Controllers\Transaction\ReceiptController::class, 'preview'])->name('receipts.preview');

    // Transactions - Delivery Notes (Surat Jalan)
    Route::resource('delivery-notes', App\Http\Controllers\Transaction\DeliveryNoteController::class);
    Route::get('/delivery-notes/{delivery_note}/pdf', [App\Http\Controllers\Transaction\DeliveryNoteController::class, 'pdf'])->name('delivery-notes.pdf');
    Route::get('/delivery-notes/{delivery_note}/preview', [App\Http\Controllers\Transaction\DeliveryNoteController::class, 'preview'])->name('delivery-notes.preview');
    Route::post('/delivery-notes/{delivery_note}/in-transit', [App\Http\Controllers\Transaction\DeliveryNoteController::class, 'markInTransit'])->name('delivery-notes.in-transit');
    Route::post('/delivery-notes/{delivery_note}/delivered', [App\Http\Controllers\Transaction\DeliveryNoteController::class, 'markDelivered'])->name('delivery-notes.delivered');
    Route::post('/delivery-notes/{delivery_note}/cancel', [App\Http\Controllers\Transaction\DeliveryNoteController::class, 'cancel'])->name('delivery-notes.cancel');

    // Finance Module
    Route::prefix('finance')->name('finance.')->group(function () {
        // Expense Categories
        Route::resource('expense-categories', App\Http\Controllers\Finance\ExpenseCategoryController::class)
            ->except(['show', 'create', 'edit']);

        // Expenses
        Route::resource('expenses', App\Http\Controllers\Finance\ExpenseTransactionController::class)
            ->except(['edit', 'update']);

        // Income
        Route::resource('income', App\Http\Controllers\Finance\IncomeTransactionController::class)
            ->except(['edit', 'update']);

        // Supplier Payables
        Route::resource('payables', App\Http\Controllers\Finance\SupplierPayableController::class)
            ->except(['edit', 'update']);
        Route::post('payables/{payable}/payment', [App\Http\Controllers\Finance\SupplierPayableController::class, 'recordPayment'])
            ->name('payables.payment');

        // Receivables
        Route::get('receivables', [App\Http\Controllers\Finance\ReceivableController::class, 'index'])->name('receivables.index');
        Route::get('receivables/{client}', [App\Http\Controllers\Finance\ReceivableController::class, 'show'])->name('receivables.show');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('profit-loss', [App\Http\Controllers\Finance\FinanceReportController::class, 'profitLoss'])->name('profit-loss');
            Route::get('receivable-aging', [App\Http\Controllers\Finance\FinanceReportController::class, 'receivableAging'])->name('receivable-aging');
            Route::get('payable-aging', [App\Http\Controllers\Finance\FinanceReportController::class, 'payableAging'])->name('payable-aging');
            Route::get('cash-flow', [App\Http\Controllers\Finance\FinanceReportController::class, 'cashFlow'])->name('cash-flow');
        });
    });
});

// Super Admin Routes
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'chartData'])->name('dashboard.chart');

    // Tenant Management
    Route::get('/tenants', function (Illuminate\Http\Request $request) {
        $query = \App\Models\Tenant::with('currentPlan', 'owner');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('superadmin.tenants.index', compact('tenants'));
    })->name('tenants.index');

    Route::get('/tenants/{tenant}', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'showTenant'])->name('tenants.show');
    Route::get('/tenants/{tenant}/edit', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'editTenant'])->name('tenants.edit');
    Route::put('/tenants/{tenant}', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'updateTenant'])->name('tenants.update');
    Route::post('/tenants/{tenant}/suspend', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'suspendTenant'])->name('tenants.suspend');
    Route::post('/tenants/{tenant}/activate', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'activateTenant'])->name('tenants.activate');
    Route::post('/tenants/{tenant}/impersonate', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'impersonate'])->name('tenants.impersonate');

    // Super Admin Settings
    Route::get('/settings/smtp', [App\Http\Controllers\SuperAdmin\SuperAdminSettingsController::class, 'smtp'])->name('settings.smtp');
    Route::post('/settings/smtp', [App\Http\Controllers\SuperAdmin\SuperAdminSettingsController::class, 'updateSmtp'])->name('settings.smtp.update');

    // Subscription Plans
    Route::get('/plans', [App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/create', [App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'store'])->name('plans.store');
    Route::get('/plans/{plan}/edit', [App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'destroy'])->name('plans.destroy');

    // Subscription Transactions
    Route::get('/subscriptions', [App\Http\Controllers\SuperAdmin\SubscriptionTransactionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}', [App\Http\Controllers\SuperAdmin\SubscriptionTransactionController::class, 'show'])->name('subscriptions.show');
    Route::post('/subscriptions/{subscription}/verify', [App\Http\Controllers\SuperAdmin\SubscriptionTransactionController::class, 'verify'])->name('subscriptions.verify');
    Route::delete('/subscriptions/{subscription}', [App\Http\Controllers\SuperAdmin\SubscriptionTransactionController::class, 'destroy'])->name('subscriptions.destroy');

    // Audit Logs
    Route::get('/audit-logs', [App\Http\Controllers\SuperAdmin\AuditLogController::class, 'index'])->name('audit-logs');
    Route::get('/audit-logs/{activityLog}', [App\Http\Controllers\SuperAdmin\AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::get('/audit-logs-export', [App\Http\Controllers\SuperAdmin\AuditLogController::class, 'export'])->name('audit-logs.export');

    // Settings
    Route::get('/settings', [App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/midtrans', [App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateMidtrans'])->name('settings.midtrans');
    Route::post('/settings/general', [App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::get('/settings/notifications', [App\Http\Controllers\SuperAdmin\SettingsController::class, 'notifications'])->name('settings.notifications');
    Route::post('/settings/notifications', [App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');

    // System Templates
    Route::get('/templates/preview/{id}', [App\Http\Controllers\SuperAdmin\TemplateController::class, 'preview'])->name('templates.preview');
    Route::resource('/templates', App\Http\Controllers\SuperAdmin\TemplateController::class);

    // API Documentation
    Route::get('/api-docs', function () {
        return view('superadmin.api-docs');
    })->name('api-docs');
});


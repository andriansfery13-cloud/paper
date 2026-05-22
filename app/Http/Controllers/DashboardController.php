<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Product;
use App\Models\InvoiceItem;
use App\Models\ActivityLog;
use App\Models\IncomeTransaction;
use App\Models\ExpenseTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
    }

    public function index()
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        // Get statistics
        $stats = $this->getDashboardStats();

        // Get recent invoices
        $recentInvoices = Invoice::with('client')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get overdue invoices
        $overdueInvoices = Invoice::with('client')
            ->where('due_date', '<', now())
            ->whereIn('status', ['sent', 'viewed', 'partial'])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Get invoices due soon (next 7 days)
        $dueSoonInvoices = Invoice::with('client')
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->whereIn('status', ['sent', 'viewed', 'partial'])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Get recent payments
        $recentPayments = Payment::with('invoice.client')
            ->successful()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get monthly revenue data for chart
        $monthlyRevenue = $this->getMonthlyRevenue();

        // NEW: Get top clients by revenue
        $topClients = $this->getTopClients();

        // NEW: Get top products by quantity sold
        $topProducts = $this->getTopProducts();

        // NEW: Get quotation conversion rate
        $conversionStats = $this->getQuotationConversionRate();

        // NEW: Get recent activities
        $recentActivities = $this->getRecentActivities();

        return view('dashboard.index', compact(
            'stats',
            'recentInvoices',
            'overdueInvoices',
            'dueSoonInvoices',
            'recentPayments',
            'monthlyRevenue',
            'topClients',
            'topProducts',
            'conversionStats',
            'recentActivities',
            'tenant'
        ));
    }

    private function getDashboardStats()
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Invoice stats
        $totalInvoices = Invoice::count();
        $unpaidInvoices = Invoice::whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])->count();
        $overdueInvoices = Invoice::where('due_date', '<', now())
            ->whereIn('status', ['sent', 'viewed', 'partial'])
            ->count();

        // Revenue stats
        $totalRevenue = Payment::successful()->sum('amount');
        $monthlyRevenue = Payment::successful()
            ->where('created_at', '>=', $currentMonth)
            ->sum('amount');
        $lastMonthRevenue = Payment::successful()
            ->whereBetween('created_at', [$lastMonth, $currentMonth])
            ->sum('amount');

        // Outstanding (Piutang)
        $totalOutstanding = Invoice::whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])
            ->sum('amount_due');

        // Client stats
        $totalClients = Client::count();
        $newClientsThisMonth = Client::where('created_at', '>=', $currentMonth)->count();

        // Quotation stats
        $pendingQuotations = Quotation::whereIn('status', ['draft', 'sent'])->count();
        $approvedQuotations = Quotation::where('status', 'approved')
            ->where('created_at', '>=', $currentMonth)
            ->count();

        // Expense stats
        $monthlyExpenses = ExpenseTransaction::where('transaction_date', '>=', $currentMonth)
            ->sum('amount');

        return [
            'total_invoices' => $totalInvoices,
            'unpaid_invoices' => $unpaidInvoices,
            'overdue_invoices' => $overdueInvoices,
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'revenue_growth' => $lastMonthRevenue > 0
                ? round(($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100, 1)
                : 0,
            'total_outstanding' => $totalOutstanding,
            'total_clients' => $totalClients,
            'new_clients' => $newClientsThisMonth,
            'pending_quotations' => $pendingQuotations,
            'approved_quotations' => $approvedQuotations,
            'monthly_expenses' => $monthlyExpenses,
            'net_income' => $monthlyRevenue - $monthlyExpenses,
        ];
    }

    private function getMonthlyRevenue()
    {
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Payment::successful()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');

            $expenses = ExpenseTransaction::whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount');

            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
                'expenses' => $expenses,
            ];
        }

        return $data;
    }

    /**
     * Get top clients by revenue (paid invoices)
     */
    private function getTopClients()
    {
        return Client::select('clients.*')
            ->selectRaw('COALESCE(SUM(payments.amount), 0) as total_revenue')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->leftJoin('payments', function ($join) {
                $join->on('invoices.id', '=', 'payments.invoice_id')
                    ->where('payments.status', 'success');
            })
            ->groupBy('clients.id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();
    }

    /**
     * Get top products by quantity sold
     */
    private function getTopProducts()
    {
        return Product::select('products.*')
            ->selectRaw('COALESCE(SUM(invoice_items.quantity), 0) as total_sold')
            ->selectRaw('COALESCE(SUM(invoice_items.subtotal), 0) as total_revenue')
            ->leftJoin('invoice_items', 'products.id', '=', 'invoice_items.product_id')
            ->leftJoin('invoices', function ($join) {
                $join->on('invoice_items.invoice_id', '=', 'invoices.id')
                    ->whereIn('invoices.status', ['paid', 'partial']);
            })
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
    }

    /**
     * Get quotation conversion rate statistics
     */
    private function getQuotationConversionRate()
    {
        $totalQuotations = Quotation::count();
        $convertedQuotations = Quotation::where('status', 'converted')->count();
        $approvedQuotations = Quotation::where('status', 'approved')->count();
        $rejectedQuotations = Quotation::where('status', 'rejected')->count();

        $conversionRate = $totalQuotations > 0
            ? round(($convertedQuotations / $totalQuotations) * 100, 1)
            : 0;

        $approvalRate = $totalQuotations > 0
            ? round((($approvedQuotations + $convertedQuotations) / $totalQuotations) * 100, 1)
            : 0;

        return [
            'total' => $totalQuotations,
            'converted' => $convertedQuotations,
            'approved' => $approvedQuotations,
            'rejected' => $rejectedQuotations,
            'conversion_rate' => $conversionRate,
            'approval_rate' => $approvalRate,
        ];
    }

    /**
     * Get recent activities for the tenant
     */
    private function getRecentActivities()
    {
        return ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function chartData(Request $request)
    {
        $type = $request->get('type', 'revenue');
        $period = $request->get('period', 6);

        switch ($type) {
            case 'revenue':
                return response()->json($this->getMonthlyRevenue());
            case 'invoices':
                return response()->json($this->getInvoiceStats($period));
            case 'quotations':
                return response()->json($this->getQuotationConversionRate());
            default:
                return response()->json([]);
        }
    }

    private function getInvoiceStats($months = 6)
    {
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);

            $created = Invoice::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $paid = Invoice::where('status', 'paid')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->count();

            $data[] = [
                'month' => $month->format('M Y'),
                'created' => $created,
                'paid' => $paid,
            ];
        }

        return $data;
    }
}


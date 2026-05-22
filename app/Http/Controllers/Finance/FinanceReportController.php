<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\IncomeTransaction;
use App\Models\ExpenseTransaction;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\SupplierPayable;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinanceReportController extends Controller
{
    public function profitLoss(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Get income by source
        $incomeBySource = IncomeTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('source, SUM(amount) as total')
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        // Get expenses by category
        $expensesByCategory = ExpenseTransaction::with('category')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->name ?? 'Tanpa Kategori',
                    'total' => $item->total,
                ];
            });

        $totalIncome = array_sum($incomeBySource);
        $totalExpenses = $expensesByCategory->sum('total');
        $netProfit = $totalIncome - $totalExpenses;

        // Monthly trend data for chart
        $monthlyData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            $monthlyData[] = [
                'month' => $monthStart->format('M Y'),
                'income' => IncomeTransaction::whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('amount'),
                'expense' => ExpenseTransaction::whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('amount'),
            ];

            $currentDate->addMonth();
        }

        return view('finance.reports.profit-loss', compact(
            'startDate',
            'endDate',
            'incomeBySource',
            'expensesByCategory',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'monthlyData'
        ));
    }

    public function receivableAging(Request $request)
    {
        // Get all unpaid invoices grouped by aging bucket
        $invoices = Invoice::with('client')
            ->whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])
            ->where('amount_due', '>', 0)
            ->get();

        $aging = [
            'current' => ['invoices' => collect(), 'total' => 0],
            '1_30' => ['invoices' => collect(), 'total' => 0],
            '31_60' => ['invoices' => collect(), 'total' => 0],
            '61_90' => ['invoices' => collect(), 'total' => 0],
            'over_90' => ['invoices' => collect(), 'total' => 0],
        ];

        foreach ($invoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->due_date, false);

            if ($daysOverdue >= 0) {
                $aging['current']['invoices']->push($invoice);
                $aging['current']['total'] += $invoice->amount_due;
            } elseif ($daysOverdue >= -30) {
                $aging['1_30']['invoices']->push($invoice);
                $aging['1_30']['total'] += $invoice->amount_due;
            } elseif ($daysOverdue >= -60) {
                $aging['31_60']['invoices']->push($invoice);
                $aging['31_60']['total'] += $invoice->amount_due;
            } elseif ($daysOverdue >= -90) {
                $aging['61_90']['invoices']->push($invoice);
                $aging['61_90']['total'] += $invoice->amount_due;
            } else {
                $aging['over_90']['invoices']->push($invoice);
                $aging['over_90']['total'] += $invoice->amount_due;
            }
        }

        $totalReceivables = collect($aging)->sum('total');

        return view('finance.reports.receivable-aging', compact('aging', 'totalReceivables'));
    }

    public function payableAging(Request $request)
    {
        // Get all unpaid payables grouped by aging bucket
        $payables = SupplierPayable::with('supplier')
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->where('amount_due', '>', 0)
            ->get();

        $aging = [
            'current' => ['payables' => collect(), 'total' => 0],
            '1_30' => ['payables' => collect(), 'total' => 0],
            '31_60' => ['payables' => collect(), 'total' => 0],
            '61_90' => ['payables' => collect(), 'total' => 0],
            'over_90' => ['payables' => collect(), 'total' => 0],
        ];

        foreach ($payables as $payable) {
            $daysOverdue = now()->diffInDays($payable->due_date, false);

            if ($daysOverdue >= 0) {
                $aging['current']['payables']->push($payable);
                $aging['current']['total'] += $payable->amount_due;
            } elseif ($daysOverdue >= -30) {
                $aging['1_30']['payables']->push($payable);
                $aging['1_30']['total'] += $payable->amount_due;
            } elseif ($daysOverdue >= -60) {
                $aging['31_60']['payables']->push($payable);
                $aging['31_60']['total'] += $payable->amount_due;
            } elseif ($daysOverdue >= -90) {
                $aging['61_90']['payables']->push($payable);
                $aging['61_90']['total'] += $payable->amount_due;
            } else {
                $aging['over_90']['payables']->push($payable);
                $aging['over_90']['total'] += $payable->amount_due;
            }
        }

        $totalPayables = collect($aging)->sum('total');

        return view('finance.reports.payable-aging', compact('aging', 'totalPayables'));
    }

    public function cashFlow(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Cash In
        $cashIn = IncomeTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        // Cash Out
        $cashOut = ExpenseTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $totalCashIn = array_sum($cashIn);
        $totalCashOut = array_sum($cashOut);
        $netCashFlow = $totalCashIn - $totalCashOut;

        // Daily cash flow for chart
        $dailyCashFlow = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dailyCashFlow[] = [
                'date' => $currentDate->format('d M'),
                'cash_in' => IncomeTransaction::whereDate('transaction_date', $dateStr)->sum('amount'),
                'cash_out' => ExpenseTransaction::whereDate('transaction_date', $dateStr)->sum('amount'),
            ];
            $currentDate->addDay();
        }

        return view('finance.reports.cash-flow', compact(
            'startDate',
            'endDate',
            'cashIn',
            'cashOut',
            'totalCashIn',
            'totalCashOut',
            'netCashFlow',
            'dailyCashFlow'
        ));
    }
}

<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\IncomeTransaction;
use Illuminate\Http\Request;

class IncomeTransactionController extends Controller
{
    public function index(Request $request)
    {
        $income = IncomeTransaction::with(['payment', 'creator'])
            ->when($request->source, function ($query, $source) {
                $query->where('source', $source);
            })
            ->when($request->start_date, function ($query, $startDate) {
                $query->where('transaction_date', '>=', $startDate);
            })
            ->when($request->end_date, function ($query, $endDate) {
                $query->where('transaction_date', '<=', $endDate);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        // Calculate totals for the filtered period
        $totalQuery = IncomeTransaction::query()
            ->when($request->source, fn($q, $s) => $q->where('source', $s))
            ->when($request->start_date, fn($q, $d) => $q->where('transaction_date', '>=', $d))
            ->when($request->end_date, fn($q, $d) => $q->where('transaction_date', '<=', $d));

        $totalIncome = $totalQuery->sum('amount');

        $sources = [
            'invoice_payment' => 'Pembayaran Invoice',
            'manual' => 'Input Manual',
            'other' => 'Lainnya',
        ];

        return view('finance.income.index', compact('income', 'totalIncome', 'sources'));
    }

    public function create()
    {
        return view('finance.income.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'account_name' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['created_by'] = auth()->id();
        $validated['source'] = 'manual';

        IncomeTransaction::create($validated);

        return redirect()->route('finance.income.index')
            ->with('success', 'Pemasukan berhasil dicatat.');
    }

    public function show(IncomeTransaction $income)
    {
        $income->load(['payment.invoice', 'creator']);

        return view('finance.income.show', compact('income'));
    }

    public function destroy(IncomeTransaction $income)
    {
        // Only allow deletion of manual entries
        if ($income->source === 'invoice_payment') {
            return redirect()->route('finance.income.index')
                ->with('error', 'Pemasukan dari pembayaran invoice tidak dapat dihapus.');
        }

        $income->delete();

        return redirect()->route('finance.income.index')
            ->with('success', 'Pemasukan berhasil dihapus.');
    }
}

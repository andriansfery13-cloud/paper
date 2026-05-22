<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ExpenseTransaction;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseTransactionController extends Controller
{
    public function index(Request $request)
    {
        $expenses = ExpenseTransaction::with(['category', 'supplier', 'creator'])
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
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

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();

        // Calculate totals for the filtered period
        $totalQuery = ExpenseTransaction::query()
            ->when($request->category_id, fn($q, $id) => $q->where('category_id', $id))
            ->when($request->start_date, fn($q, $d) => $q->where('transaction_date', '>=', $d))
            ->when($request->end_date, fn($q, $d) => $q->where('transaction_date', '<=', $d));

        $totalExpenses = $totalQuery->sum('amount');

        return view('finance.expenses.index', compact('expenses', 'categories', 'totalExpenses'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('finance.expenses.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'account_name' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'receipt_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['created_by'] = auth()->id();

        // Handle receipt image upload
        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = $request->file('receipt_image')
                ->store('receipts', 'public');
        }

        ExpenseTransaction::create($validated);

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function show(ExpenseTransaction $expense)
    {
        $expense->load(['category', 'supplier', 'creator']);

        return view('finance.expenses.show', compact('expense'));
    }

    public function destroy(ExpenseTransaction $expense)
    {
        // Delete receipt image if exists
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }

        $expense->delete();

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}

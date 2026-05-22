<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ExpenseCategory::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15);

        return view('finance.expense-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        ExpenseCategory::create($validated);

        return redirect()->route('finance.expense-categories.index')
            ->with('success', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $expense_category->update($validated);

        return redirect()->route('finance.expense-categories.index')
            ->with('success', 'Kategori pengeluaran berhasil diperbarui.');
    }

    public function destroy(ExpenseCategory $expense_category)
    {
        // Check if category has transactions
        if ($expense_category->expenses()->exists()) {
            return redirect()->route('finance.expense-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki transaksi.');
        }

        $expense_category->delete();

        return redirect()->route('finance.expense-categories.index')
            ->with('success', 'Kategori pengeluaran berhasil dihapus.');
    }
}

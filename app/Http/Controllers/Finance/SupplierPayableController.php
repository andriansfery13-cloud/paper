<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayable;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierPayableController extends Controller
{
    public function index(Request $request)
    {
        $payables = SupplierPayable::with(['supplier', 'creator'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->supplier_id, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        $suppliers = Supplier::orderBy('name')->get();

        // Calculate totals
        $totalQuery = SupplierPayable::query()
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->supplier_id, fn($q, $id) => $q->where('supplier_id', $id));

        $totalPayables = $totalQuery->sum('amount');
        $totalDue = $totalQuery->sum('amount_due');

        $statuses = [
            'unpaid' => 'Belum Dibayar',
            'partial' => 'Dibayar Sebagian',
            'paid' => 'Lunas',
            'overdue' => 'Jatuh Tempo',
        ];

        return view('finance.payables.index', compact('payables', 'suppliers', 'totalPayables', 'totalDue', 'statuses'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('finance.payables.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'transaction_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:transaction_date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'unpaid';
        $validated['amount_paid'] = 0;
        $validated['amount_due'] = $validated['amount'];

        SupplierPayable::create($validated);

        return redirect()->route('finance.payables.index')
            ->with('success', 'Hutang supplier berhasil ditambahkan.');
    }

    public function show(SupplierPayable $payable)
    {
        $payable->load(['supplier', 'creator', 'payments.creator']);

        return view('finance.payables.show', compact('payable'));
    }

    public function recordPayment(Request $request, SupplierPayable $payable)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $payable->amount_due,
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $payable->recordPayment(
            $validated['amount'],
            $validated['payment_method'],
            $validated['reference_number'] ?? null,
            $validated['notes'] ?? null
        );

        return redirect()->route('finance.payables.show', $payable)
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function destroy(SupplierPayable $payable)
    {
        // Only allow deletion if no payments have been made
        if ($payable->payments()->exists()) {
            return redirect()->route('finance.payables.index')
                ->with('error', 'Hutang tidak dapat dihapus karena sudah ada pembayaran.');
        }

        $payable->delete();

        return redirect()->route('finance.payables.index')
            ->with('success', 'Hutang supplier berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
        $this->middleware('permission:payments.view')->only(['index', 'show']);
        $this->middleware('permission:payments.create')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $query = Payment::with(['invoice.client', 'receiver']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                    ->orWhereHas('invoice', function ($q2) use ($search) {
                        $q2->where('invoice_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('transactions.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $invoice = null;
        if ($request->has('invoice_id')) {
            $invoice = Invoice::with('client')->findOrFail($request->invoice_id);
        }

        $unpaidInvoices = Invoice::with('client')
            ->whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])
            ->where('amount_due', '>', 0)
            ->orderBy('due_date')
            ->get();

        return view('transactions.payments.create', compact('invoice', 'unpaidInvoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'proof_of_payment' => 'nullable|image|max:2048',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // Check if amount exceeds due
        if ($validated['amount'] > $invoice->amount_due) {
            return back()->withErrors(['amount' => 'Jumlah pembayaran melebihi sisa tagihan']);
        }

        // Handle proof upload
        $proofPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofPath = $request->file('proof_of_payment')->store('payment-proofs', 'public');
        }

        $payment = Payment::create([
            'tenant_id' => auth()->user()->tenant_id,
            'invoice_id' => $validated['invoice_id'],
            'received_by' => auth()->id(),
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'],
            'notes' => $validated['notes'],
            'proof_of_payment' => $proofPath,
            'status' => 'success',
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Pembayaran berhasil dicatat');
    }

    public function show(Payment $payment)
    {
        $payment->load(['invoice.client', 'receiver', 'receipt']);
        return view('transactions.payments.show', compact('payment'));
    }
}

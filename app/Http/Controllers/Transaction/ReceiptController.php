<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
    }

    /**
     * Display a listing of receipts
     */
    public function index(Request $request)
    {
        $query = Receipt::with(['invoice.client', 'payment']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('invoice', function ($q2) use ($search) {
                        $q2->where('invoice_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('invoice.client', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $receipts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => Receipt::count(),
            'this_month' => Receipt::whereMonth('receipt_date', now()->month)->count(),
            'total_amount' => Receipt::sum('amount'),
        ];

        return view('transactions.receipts.index', compact('receipts', 'stats'));
    }

    /**
     * Show the form for creating a new receipt
     */
    public function create(Request $request)
    {
        // Must create receipt from an invoice
        if (!$request->has('invoice_id')) {
            return redirect()->route('invoices.index')->with('error', 'Silakan pilih invoice terlebih dahulu');
        }

        $invoice = Invoice::with(['client', 'payments'])->findOrFail($request->invoice_id);

        // Check if invoice has unpaid amount
        if ($invoice->amount_due <= 0) {
            return redirect()->route('invoices.show', $invoice)->with('warning', 'Invoice sudah lunas');
        }

        // Generate suggested number
        $tenant = auth()->user()->tenant;
        $nextNumber = Receipt::generateNumber($tenant->id);

        return view('transactions.receipts.create', compact('invoice', 'nextNumber'));
    }

    /**
     * Store a newly created receipt
     */
    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'receipt_number' => 'required|string|max:50|unique:receipts,receipt_number,NULL,id,tenant_id,' . $tenant->id,
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
            'include_signature' => 'boolean',
            'include_stamp' => 'boolean',
            'include_qr' => 'boolean',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // Create payment first
        $payment = Payment::create([
            'tenant_id' => auth()->user()->tenant_id,
            'invoice_id' => $invoice->id,
            'created_by' => auth()->id(),
            'payment_date' => $validated['receipt_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'success',
        ]);

        // Create receipt
        $receipt = Receipt::create([
            'tenant_id' => auth()->user()->tenant_id,
            'invoice_id' => $invoice->id,
            'payment_id' => $payment->id,
            'receipt_number' => $validated['receipt_number'],
            'created_by' => auth()->id(),
            'receipt_date' => $validated['receipt_date'],
            'amount' => $validated['amount'],
            'amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? null,
            'include_signature' => $request->has('include_signature'),
            'include_stamp' => $request->has('include_stamp'),
            'include_qr' => $request->has('include_qr'),
        ]);

        // Update invoice payment
        $invoice->recordPayment($validated['amount']);

        // If fully paid, mark as paid
        if ($invoice->amount_due <= 0) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        }

        // Redirect to receipt show page (which auto-prints)
        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'Kwitansi berhasil dibuat. Invoice telah diupdate.');
    }

    /**
     * Display the specified receipt
     */
    public function show(Receipt $receipt)
    {
        $receipt->load(['invoice.client', 'payment', 'creator', 'invoice.tenant']);
        return view('transactions.receipts.show', compact('receipt'));
    }

    /**
     * Generate PDF receipt
     */
    public function pdf(Receipt $receipt)
    {
        $receipt->load(['invoice.client', 'invoice.tenant', 'payment']);
        $pdf = Pdf::loadView('pdf.receipt', compact('receipt'));
        return $pdf->download("Kwitansi-{$receipt->receipt_number}.pdf");
    }

    /**
     * Preview PDF receipt
     */
    public function preview(Receipt $receipt)
    {
        $receipt->load(['invoice.client', 'invoice.tenant', 'payment']);
        $pdf = Pdf::loadView('pdf.receipt', compact('receipt'));
        return $pdf->stream("Kwitansi-{$receipt->receipt_number}.pdf");
    }

    /**
     * Delete receipt (also removes payment)
     */
    public function destroy(Receipt $receipt)
    {
        $invoice = $receipt->invoice;
        $amount = $receipt->amount;

        // Reverse the payment
        if ($receipt->payment) {
            $receipt->payment->delete();
        }

        // Update invoice
        $invoice->decrement('amount_paid', $amount);
        $invoice->amount_due = $invoice->total - $invoice->amount_paid;

        if ($invoice->amount_due > 0 && $invoice->status === 'paid') {
            $invoice->status = $invoice->amount_paid > 0 ? 'partial' : 'sent';
            $invoice->paid_at = null;
        }
        $invoice->save();

        $receipt->delete();

        return redirect()->route('receipts.index')
            ->with('success', 'Kwitansi berhasil dihapus');
    }

    public function sendAuto(Receipt $receipt, \App\Services\NotificationService $notificationService)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->canUseWaGateway()) {
            return back()->with('error', 'Fitur Kirim Auto (WhatsApp) tidak tersedia di paket langganan Anda. Silakan upgrade paket untuk menggunakan fitur ini.');
        }

        $waEnabled = \App\Models\Setting::get('whatsapp_enabled', 'false') === 'true';
        $results = [];

        if ($waEnabled && $receipt->invoice->client->phone) {
            try {
                $notificationService->notifyReceiptCreated($receipt);
                $results[] = 'WhatsApp terkirim';
            } catch (\Exception $e) {
                $results[] = 'WhatsApp gagal';
            }
        } elseif (!$waEnabled) {
            $results[] = 'WhatsApp Gateway belum dikonfigurasi admin';
        }

        return back()->with('success', 'Proses kirim auto selesai: ' . implode(', ', $results));
    }
}

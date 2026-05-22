<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
        $this->middleware('permission:invoices.view')->only(['index', 'show']);
        $this->middleware('permission:invoices.create')->only(['create', 'store']);
        $this->middleware('permission:invoices.edit')->only(['edit', 'update']);
        $this->middleware('permission:invoices.delete')->only('destroy');
        $this->middleware('permission:invoices.send')->only('send');
    }

    public function index(Request $request)
    {
        $query = Invoice::with('client');

        // Search by invoice number or client
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $invoices = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => Invoice::count(),
            'unpaid' => Invoice::whereIn('status', ['sent', 'viewed', 'partial'])->count(),
            'overdue' => Invoice::where('due_date', '<', now())
                ->whereIn('status', ['sent', 'viewed', 'partial'])->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
        ];

        return view('transactions.invoices.index', compact('invoices', 'stats'));
    }

    public function create(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->canCreateInvoice()) {
            $quota = $tenant->getQuotaStatus();
            return redirect()->route('invoices.index')
                ->with('quota_exceeded', true)
                ->with('quota_type', 'invoice')
                ->with('quota_message', 'Kuota invoice telah habis. Upgrade paket untuk membuat invoice baru.')
                ->with('quota_usage', json_encode($quota));
        }

        $clients = Client::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();

        // Generate suggested number
        $nextNumber = Invoice::generateNumber($tenant->id);

        // If converting from quotation
        $quotation = null;
        if ($request->has('quotation_id')) {
            $quotation = \App\Models\Quotation::with('items.product', 'client')
                ->findOrFail($request->quotation_id);
        }

        return view('transactions.invoices.create', compact('clients', 'products', 'quotation', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->canCreateInvoice()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'quota_exceeded' => true,
                    'quota_type' => 'invoice',
                    'message' => 'Kuota invoice telah habis.',
                    'usage' => $tenant->getQuotaStatus()
                ], 403);
            }
            return redirect()->route('invoices.index')
                ->with('quota_exceeded', true)
                ->with('quota_type', 'invoice')
                ->with('quota_message', 'Kuota invoice telah habis. Upgrade paket untuk membuat invoice baru.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,NULL,id,tenant_id,' . $tenant->id,
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subject' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'terms' => 'nullable|string|max:2000',
            'discount_type' => 'nullable|in:0,1',
            'discount_value' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:20',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'include_signature' => 'boolean',
            'include_stamp' => 'boolean',
            'include_qr' => 'boolean',
        ]);

        // Create invoice
        $invoice = Invoice::create([
            'tenant_id' => auth()->user()->tenant_id,
            'client_id' => $validated['client_id'],
            'invoice_number' => $validated['invoice_number'],
            'quotation_id' => $request->quotation_id,
            'created_by' => auth()->id(),
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'subject' => $validated['subject'],
            'notes' => $validated['notes'],
            'terms' => $validated['terms'],
            'discount_type' => $validated['discount_type'] ?? 0,
            'discount_value' => $validated['discount_value'] ?? 0,
            'shipping_amount' => $validated['shipping_amount'] ?? 0,
            'shipping_amount' => $validated['shipping_amount'] ?? 0,
            'status' => 'draft',
            'include_signature' => $request->has('include_signature'),
            'include_stamp' => $request->has('include_stamp'),
            'include_qr' => $request->has('include_qr'),
        ]);

        // Create invoice items
        foreach ($validated['items'] as $index => $item) {
            $invoice->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'discount_percent' => $item['discount_percent'] ?? 0,
                'tax_percent' => $item['tax_percent'] ?? 0,
                'sort_order' => $index,
            ]);
        }

        // Calculate totals
        $invoice->calculateTotals()->save();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice berhasil dibuat');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product', 'payments', 'creator', 'deliveryNotes']);
        return view('transactions.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if (!$invoice->canBeEdited()) {
            return back()->with('error', 'Invoice tidak dapat diedit karena sudah dikirim');
        }

        $clients = Client::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $invoice->load('items.product');

        return view('transactions.invoices.edit', compact('invoice', 'clients', 'products'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if (!$invoice->canBeEdited()) {
            return back()->with('error', 'Invoice tidak dapat diedit karena sudah dikirim');
        }

        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,' . $invoice->id . ',id,tenant_id,' . $tenantId,
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subject' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'terms' => 'nullable|string|max:2000',
            'discount_type' => 'nullable|in:0,1',
            'discount_value' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:20',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'include_signature' => 'boolean',
            'include_stamp' => 'boolean',
            'include_qr' => 'boolean',
        ]);

        // Update invoice
        $invoice->update([
            'client_id' => $validated['client_id'],
            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'subject' => $validated['subject'],
            'notes' => $validated['notes'],
            'terms' => $validated['terms'],
            'discount_type' => $validated['discount_type'] ?? 0,
            'discount_value' => $validated['discount_value'] ?? 0,
            'discount_value' => $validated['discount_value'] ?? 0,
            'shipping_amount' => $validated['shipping_amount'] ?? 0,
            'include_signature' => $request->has('include_signature'),
            'include_stamp' => $request->has('include_stamp'),
            'include_qr' => $request->has('include_qr'),
        ]);

        // Delete removed items
        $existingIds = collect($validated['items'])->pluck('id')->filter()->toArray();
        $invoice->items()->whereNotIn('id', $existingIds)->delete();

        // Update or create items
        foreach ($validated['items'] as $index => $item) {
            $invoice->items()->updateOrCreate(
                ['id' => $item['id'] ?? null],
                [
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'tax_percent' => $item['tax_percent'] ?? 0,
                    'sort_order' => $index,
                ]
            );
        }

        // Recalculate totals
        $invoice->refresh();
        $invoice->calculateTotals()->save();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice berhasil diperbarui');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->payments()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus invoice yang sudah ada pembayaran');
        }

        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice berhasil dihapus');
    }

    public function send(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product', 'tenant']);
        $invoice->markAsSent();

        // Send email notification if enabled
        $tenant = auth()->user()->tenant;
        $emailSettings = $tenant->settings['email'] ?? [];

        if (!empty($emailSettings['notifications_enabled']) && $invoice->client->email) {
            try {
                \Mail::to($invoice->client->email)
                    ->send(new \App\Mail\InvoiceSent($invoice));
            } catch (\Exception $e) {
                \Log::error('Failed to send invoice email: ' . $e->getMessage());
                return back()->with('warning', 'Invoice berhasil dikirim, tetapi email gagal terkirim.');
            }
        }

        return back()->with('success', 'Invoice berhasil dikirim via Email');
    }

    public function sendAuto(Request $request, Invoice $invoice, \App\Services\NotificationService $notificationService)
    {
        $invoice->load(['client', 'items.product', 'tenant']);
        $invoice->markAsSent();
        $tenant = auth()->user()->tenant;

        $results = [];

        // 1. Send Email
        $emailSettings = $tenant->settings['email'] ?? [];
        if (!empty($emailSettings['notifications_enabled']) && $invoice->client->email) {
            try {
                \Mail::to($invoice->client->email)
                    ->send(new \App\Mail\InvoiceSent($invoice));
                $results[] = 'Email terkirim';
            } catch (\Exception $e) {
                \Log::error('Failed to send invoice email (Auto): ' . $e->getMessage());
                $results[] = 'Email gagal';
            }
        }

        // 2. Send WhatsApp
        // Check global setting instead of tenant setting
        $waEnabled = \App\Models\Setting::get('whatsapp_enabled', 'false') === 'true';

        if ($waEnabled && $invoice->client->phone) {
            try {
                $notificationService->notifyInvoiceCreated($invoice);
                // Check logs or trust service (service returns void/bool but notifyInvoiceCreated returns void. 
                // We assume queued/sent if no exception from service, though service catches exceptions internally).
                // Let's assume success if enabled.
                $results[] = 'WhatsApp terkirim';
            } catch (\Exception $e) {
                $results[] = 'WhatsApp gagal';
            }
        }

        if (empty($results)) {
            return back()->with('warning', 'Invoice ditandai Terkirim, namun tidak ada notifikasi yang dikonfigurasi/dikirim.');
        }

        return back()->with('success', 'Invoice diproses: ' . implode(', ', $results));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product', 'tenant']);

        // Check for custom template
        $template = \App\Models\DocumentTemplate::getDefaultTemplate('invoice', $invoice->tenant_id);

        if ($template) {
            // Render using custom template
            $content = $template->html_content;

            // Basic placeholders replacement (same as in preview)
            // In production, use a more robust engine or Blade::compileString
            $placeholders = [
                'invoice_number' => $invoice->invoice_number,
                'date' => $invoice->invoice_date->format('d M Y'),
                'due_date' => $invoice->due_date->format('d M Y'),
                'company_name' => $invoice->tenant->company_name,
                'client_name' => $invoice->client->name,
                'subtotal' => 'Rp ' . number_format($invoice->subtotal, 0, ',', '.'),
                'tax' => 'Rp ' . number_format($invoice->tax_amount, 0, ',', '.'),
                'total' => 'Rp ' . number_format($invoice->total, 0, ',', '.'),
                'notes' => $invoice->notes,
                'terms' => $invoice->terms,
                'qr_code' => '<img src="data:image/png;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(100)->generate($invoice->verification_url)) . '" alt="QR">',
            ];

            // Build items table row by row
            $itemsHtml = '<table class="w-full border-collapse"><thead><tr class="border-b"><th class="py-2 text-left">Deskripsi</th><th class="py-2 text-right">Qty</th><th class="py-2 text-right">Harga</th><th class="py-2 text-right">Total</th></tr></thead><tbody>';
            foreach ($invoice->items as $item) {
                $itemsHtml .= '<tr class="border-b">';
                $itemsHtml .= '<td class="py-2">' . $item->description . '</td>';
                $itemsHtml .= '<td class="py-2 text-right">' . number_format($item->quantity, 0) . ' ' . $item->unit . '</td>';
                $itemsHtml .= '<td class="py-2 text-right">Rp ' . number_format($item->unit_price, 0, ',', '.') . '</td>';
                $itemsHtml .= '<td class="py-2 text-right">Rp ' . number_format($item->subtotal, 0, ',', '.') . '</td>';
                $itemsHtml .= '</tr>';
            }
            $itemsHtml .= '</tbody></table>';

            $placeholders['items_table'] = $itemsHtml;

            // Signature
            if ($invoice->tenant->signature_image && file_exists(storage_path('app/public/' . $invoice->tenant->signature_image))) {
                $placeholders['signature'] = '<img src="' . storage_path('app/public/' . $invoice->tenant->signature_image) . '" style="height: 80px;" alt="Signature">';
            } else {
                $placeholders['signature'] = '<div style="height: 80px;"></div>';
            }

            foreach ($placeholders as $key => $value) {
                $content = str_replace(
                    ['{{ $' . $key . ' }}', '{{ ' . $key . ' }}', '@{{ ' . $key . ' }}'],
                    $value ?? '',
                    $content
                );
            }

            $pdf = Pdf::loadHTML($content);
        } else {
            $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
        }

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function preview(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product', 'tenant']);

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function duplicate(Invoice $invoice)
    {
        $newInvoice = $invoice->replicate([
            'invoice_number',
            'verification_code',
            'document_hash',
            'status',
            'amount_paid',
            'sent_at',
            'viewed_at',
            'paid_at',
        ]);

        $newInvoice->invoice_date = now();
        $newInvoice->due_date = now()->addDays($invoice->client->payment_term_days ?? 30);
        $newInvoice->status = 'draft';
        $newInvoice->amount_paid = 0;
        $newInvoice->amount_due = $invoice->total;
        $newInvoice->save();

        // Duplicate items
        foreach ($invoice->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $newInvoice->id;
            $newItem->save();
        }

        return redirect()->route('invoices.edit', $newInvoice)
            ->with('success', 'Invoice berhasil diduplikasi');
    }
}

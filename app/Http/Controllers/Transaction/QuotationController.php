<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
        $this->middleware('permission:quotations.view')->only(['index', 'show']);
        $this->middleware('permission:quotations.create')->only(['create', 'store']);
        $this->middleware('permission:quotations.edit')->only(['edit', 'update']);
        $this->middleware('permission:quotations.delete')->only('destroy');
        $this->middleware('permission:quotations.approve')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        $query = Quotation::with('client');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quotations = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => Quotation::count(),
            'pending' => Quotation::whereIn('status', ['draft', 'sent'])->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'rejected' => Quotation::where('status', 'rejected')->count(),
        ];

        return view('transactions.quotations.index', compact('quotations', 'stats'));
    }

    public function create()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->canCreateQuotation()) {
            $quota = $tenant->getQuotaStatus();
            return redirect()->route('quotations.index')
                ->with('quota_exceeded', true)
                ->with('quota_type', 'quotation')
                ->with('quota_message', 'Kuota penawaran telah habis. Upgrade paket untuk membuat penawaran baru.')
                ->with('quota_usage', json_encode($quota));
        }

        $clients = Client::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();

        // Generate suggested number
        $nextNumber = Quotation::generateNumber($tenant->id);

        return view('transactions.quotations.create', compact('clients', 'products', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        // Check quota... (omitted for brevity, keep existing logic)
        if (!$tenant->canCreateQuotation()) {
            // ... existing quota check logic
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'quota_exceeded' => true,
                    'quota_type' => 'quotation',
                    'message' => 'Kuota penawaran telah habis.',
                    'usage' => $tenant->getQuotaStatus()
                ], 403);
            }
            return redirect()->route('quotations.index')
                ->with('quota_exceeded', true)
                ->with('quota_type', 'quotation')
                ->with('quota_message', 'Kuota penawaran telah habis. Upgrade paket untuk membuat penawaran baru.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quotation_number' => 'required|string|max:50|unique:quotations,quotation_number,NULL,id,tenant_id,' . $tenant->id,
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:quotation_date',
            'subject' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'terms' => 'nullable|string|max:2000',
            'discount_type' => 'nullable|in:0,1',
            'discount_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:20',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'include_signature' => 'boolean',
            'include_stamp' => 'boolean',
            'include_qr' => 'boolean',
        ]);

        $quotation = Quotation::create([
            'tenant_id' => auth()->user()->tenant_id,
            'client_id' => $validated['client_id'],
            'quotation_number' => $validated['quotation_number'],
            'created_by' => auth()->id(),
            'quotation_date' => $validated['quotation_date'],
            'valid_until' => $validated['valid_until'],
            'subject' => $validated['subject'],
            'notes' => $validated['notes'],
            'terms' => $validated['terms'],
            'discount_type' => $validated['discount_type'] ?? 0,
            'discount_value' => $validated['discount_value'] ?? 0,
            'status' => 'draft',
            'include_signature' => $request->has('include_signature'),
            'include_stamp' => $request->has('include_stamp'),
            'include_qr' => $request->has('include_qr'),
        ]);

        foreach ($validated['items'] as $index => $item) {
            $quotation->items()->create([
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

        $quotation->calculateTotals()->save();

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation berhasil dibuat');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['client', 'items.product', 'creator', 'approver']);
        return view('transactions.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        if (!$quotation->canBeEdited()) {
            return back()->with('error', 'Quotation tidak dapat diedit');
        }

        $clients = Client::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $quotation->load('items.product');

        return view('transactions.quotations.edit', compact('quotation', 'clients', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if (!$quotation->canBeEdited()) {
            return back()->with('error', 'Quotation tidak dapat diedit');
        }

        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quotation_number' => 'required|string|max:50|unique:quotations,quotation_number,' . $quotation->id . ',id,tenant_id,' . $tenantId,
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:quotation_date',
            'subject' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'terms' => 'nullable|string|max:2000',
            'discount_type' => 'nullable|in:0,1',
            'discount_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:quotation_items,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:20',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'include_signature' => 'boolean',
            'include_stamp' => 'boolean',
            'include_qr' => 'boolean',
        ]);

        $quotation->update([
            'client_id' => $validated['client_id'],
            'quotation_number' => $validated['quotation_number'],
            'quotation_date' => $validated['quotation_date'],
            'valid_until' => $validated['valid_until'],
            'subject' => $validated['subject'],
            'notes' => $validated['notes'],
            'terms' => $validated['terms'],
            'discount_type' => $validated['discount_type'] ?? 0,
            'discount_value' => $validated['discount_value'] ?? 0,
            'include_signature' => $request->has('include_signature'),
            'include_stamp' => $request->has('include_stamp'),
            'include_qr' => $request->has('include_qr'),
        ]);

        $existingIds = collect($validated['items'])->pluck('id')->filter()->toArray();
        $quotation->items()->whereNotIn('id', $existingIds)->delete();

        foreach ($validated['items'] as $index => $item) {
            $quotation->items()->updateOrCreate(
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

        $quotation->refresh();
        $quotation->calculateTotals()->save();

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation berhasil diperbarui');
    }

    public function destroy(Quotation $quotation)
    {
        if ($quotation->invoices()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus quotation yang sudah dikonversi ke invoice');
        }

        $quotation->items()->delete();
        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation berhasil dihapus');
    }

    public function send(Quotation $quotation)
    {
        $quotation->load(['client', 'items.product', 'tenant']);
        $quotation->markAsSent();

        // Send email notification if enabled
        $tenant = auth()->user()->tenant;
        $emailSettings = $tenant->settings['email'] ?? [];

        if (!empty($emailSettings['notifications_enabled']) && $quotation->client->email) {
            try {
                \Mail::to($quotation->client->email)
                    ->send(new \App\Mail\QuotationSent($quotation));
            } catch (\Exception $e) {
                \Log::error('Failed to send quotation email: ' . $e->getMessage());
                return back()->with('warning', 'Quotation berhasil dikirim, tetapi email gagal terkirim.');
            }
        }

        return back()->with('success', 'Quotation berhasil dikirim');
    }

    public function sendAuto(Quotation $quotation, \App\Services\NotificationService $notificationService)
    {
        $tenant = auth()->user()->tenant;

        // Check if plan allows WA Gateway
        if (!$tenant->canUseWaGateway()) {
            return back()->with('error', 'Fitur Kirim Auto (WhatsApp) tidak tersedia di paket langganan Anda. Silakan upgrade paket untuk menggunakan fitur ini.');
        }

        $quotation->load(['client', 'items.product', 'tenant']);

        $results = [];

        // 1. Send Email (Standard logic)
        $emailSettings = $tenant->settings['email'] ?? [];
        if (!empty($emailSettings['notifications_enabled']) && $quotation->client->email) {
            try {
                \Mail::to($quotation->client->email)
                    ->send(new \App\Mail\QuotationSent($quotation));
                $results[] = 'Email terkirim';
            } catch (\Exception $e) {
                $results[] = 'Email gagal';
            }
        }

        // 2. Send WhatsApp
        // Check global setting instead of tenant setting
        $waEnabled = \App\Models\Setting::get('whatsapp_enabled', 'false') === 'true';

        if ($waEnabled && $quotation->client->phone) {
            try {
                $notificationService->notifyQuotationCreated($quotation);
                $results[] = 'WhatsApp terkirim';
            } catch (\Exception $e) {
                $results[] = 'WhatsApp gagal';
            }
        } elseif (!$waEnabled) {
            $results[] = 'WhatsApp Gateway belum dikonfigurasi admin';
        }

        $quotation->markAsSent();

        return back()->with('success', 'Proses kirim auto selesai: ' . implode(', ', $results));
    }

    public function approve(Request $request, Quotation $quotation)
    {
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return back()->with('error', 'Status quotation tidak dapat diapprove');
        }

        $quotation->approve(auth()->id());

        return back()->with('success', 'Quotation berhasil diapprove');
    }

    public function reject(Request $request, Quotation $quotation)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return back()->with('error', 'Status quotation tidak dapat direject');
        }

        $quotation->reject($request->rejection_reason);

        return back()->with('success', 'Quotation berhasil direject');
    }

    public function convertToInvoice(Quotation $quotation)
    {
        if (!$quotation->canBeConverted()) {
            return back()->with('error', 'Quotation tidak dapat dikonversi ke invoice');
        }

        $invoice = $quotation->convertToInvoice();

        if (!$invoice) {
            return back()->with('error', 'Gagal mengkonversi quotation');
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Quotation berhasil dikonversi ke Invoice');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['client', 'items.product', 'tenant']);
        $pdf = Pdf::loadView('pdf.quotation', compact('quotation'));
        return $pdf->download("Quotation-{$quotation->quotation_number}.pdf");
    }

    public function preview(Quotation $quotation)
    {
        $quotation->load(['client', 'items.product', 'tenant']);
        $pdf = Pdf::loadView('pdf.quotation', compact('quotation'));
        return $pdf->stream("Quotation-{$quotation->quotation_number}.pdf");
    }
}

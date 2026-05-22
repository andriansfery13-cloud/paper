<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
    }

    /**
     * Display a listing of delivery notes
     */
    public function index(Request $request)
    {
        $query = DeliveryNote::with(['invoice.client']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhereHas('invoice', function ($q2) use ($search) {
                        $q2->where('invoice_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $deliveryNotes = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => DeliveryNote::count(),
            'pending' => DeliveryNote::where('status', 'pending')->count(),
            'in_transit' => DeliveryNote::where('status', 'in_transit')->count(),
            'delivered' => DeliveryNote::where('status', 'delivered')->count(),
        ];

        return view('transactions.delivery-notes.index', compact('deliveryNotes', 'stats'));
    }

    /**
     * Show the form for creating a new delivery note
     */
    public function create(Request $request)
    {
        if (!$request->has('invoice_id')) {
            return redirect()->route('invoices.index')->with('error', 'Silakan pilih invoice terlebih dahulu');
        }

        $invoice = Invoice::with(['client', 'items.product'])->findOrFail($request->invoice_id);

        // Generate suggested number
        $tenant = auth()->user()->tenant;
        $nextNumber = DeliveryNote::generateNumber($tenant->id);

        return view('transactions.delivery-notes.create', compact('invoice', 'nextNumber'));
    }

    /**
     * Store a newly created delivery note
     */
    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'delivery_number' => 'required|string|max:50|unique:delivery_notes,delivery_number,NULL,id,tenant_id,' . $tenant->id,
            'delivery_date' => 'required|date',
            'recipient_name' => 'required|string|max:255',
            'recipient_address' => 'nullable|string|max:500',
            'recipient_phone' => 'nullable|string|max:20',
            'driver_name' => 'nullable|string|max:100',
            'vehicle_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:20',
            'items.*.notes' => 'nullable|string|max:255',
            'items.*.notes' => 'nullable|string|max:255',
            'include_signature' => 'boolean',
            'include_stamp' => 'boolean',
            'include_qr' => 'boolean',
        ]);

        $deliveryNote = DeliveryNote::create([
            'tenant_id' => auth()->user()->tenant_id,
            'invoice_id' => $validated['invoice_id'],
            'delivery_number' => $validated['delivery_number'],
            'created_by' => auth()->id(),
            'delivery_date' => $validated['delivery_date'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_address' => $validated['recipient_address'] ?? null,
            'recipient_phone' => $validated['recipient_phone'] ?? null,
            'driver_name' => $validated['driver_name'] ?? null,
            'vehicle_number' => $validated['vehicle_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'include_signature' => $request->has('include_signature'),
            'include_stamp' => $request->has('include_stamp'),
            'include_qr' => $request->has('include_qr'),
        ]);

        foreach ($validated['items'] as $index => $item) {
            $deliveryNote->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'notes' => $item['notes'] ?? null,
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('delivery-notes.show', $deliveryNote)
            ->with('success', 'Surat Jalan berhasil dibuat');
    }

    /**
     * Display the specified delivery note
     */
    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['invoice.client', 'invoice.tenant', 'items', 'creator']);
        return view('transactions.delivery-notes.show', compact('deliveryNote'));
    }

    /**
     * Show the form for editing the delivery note
     */
    public function edit(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status === 'delivered') {
            return back()->with('error', 'Surat Jalan yang sudah terkirim tidak dapat diedit');
        }

        $deliveryNote->load(['invoice.client', 'items']);
        return view('transactions.delivery-notes.edit', compact('deliveryNote'));
    }

    /**
     * Update the delivery note
     */
    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status === 'delivered') {
            return back()->with('error', 'Surat Jalan yang sudah terkirim tidak dapat diedit');
        }

        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'recipient_name' => 'required|string|max:255',
            'recipient_address' => 'nullable|string|max:500',
            'recipient_phone' => 'nullable|string|max:20',
            'driver_name' => 'nullable|string|max:100',
            'vehicle_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:delivery_note_items,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:20',
            'items.*.notes' => 'nullable|string|max:255',
            'items.*.notes' => 'nullable|string|max:255',
            'include_signature' => 'boolean',
            'include_stamp' => 'boolean',
            'include_qr' => 'boolean',
        ]);

        $deliveryNote->update([
            'delivery_date' => $validated['delivery_date'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_address' => $validated['recipient_address'] ?? null,
            'recipient_phone' => $validated['recipient_phone'] ?? null,
            'driver_name' => $validated['driver_name'] ?? null,
            'vehicle_number' => $validated['vehicle_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'include_signature' => $request->has('include_signature'),
            'include_stamp' => $request->has('include_stamp'),
            'include_qr' => $request->has('include_qr'),
        ]);

        // Update items
        $existingIds = collect($validated['items'])->pluck('id')->filter()->toArray();
        $deliveryNote->items()->whereNotIn('id', $existingIds)->delete();

        foreach ($validated['items'] as $index => $item) {
            $deliveryNote->items()->updateOrCreate(
                ['id' => $item['id'] ?? null],
                [
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'notes' => $item['notes'] ?? null,
                    'sort_order' => $index,
                ]
            );
        }

        return redirect()->route('delivery-notes.show', $deliveryNote)
            ->with('success', 'Surat Jalan berhasil diperbarui');
    }

    /**
     * Mark as in transit
     */
    public function markInTransit(DeliveryNote $deliveryNote)
    {
        $deliveryNote->markAsInTransit();
        return back()->with('success', 'Status diubah ke Dalam Perjalanan');
    }

    /**
     * Mark as delivered
     */
    public function markDelivered(Request $request, DeliveryNote $deliveryNote)
    {
        $request->validate([
            'received_by_name' => 'required|string|max:100',
        ]);

        $deliveryNote->markAsDelivered($request->received_by_name);
        return back()->with('success', 'Surat Jalan telah dikonfirmasi terkirim');
    }

    /**
     * Cancel delivery note
     */
    public function cancel(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status === 'delivered') {
            return back()->with('error', 'Tidak dapat membatalkan Surat Jalan yang sudah terkirim');
        }

        $deliveryNote->cancel();
        return back()->with('success', 'Surat Jalan dibatalkan');
    }

    /**
     * Delete delivery note
     */
    public function destroy(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status === 'delivered') {
            return back()->with('error', 'Tidak dapat menghapus Surat Jalan yang sudah terkirim');
        }

        $deliveryNote->items()->delete();
        $deliveryNote->delete();

        return redirect()->route('delivery-notes.index')
            ->with('success', 'Surat Jalan berhasil dihapus');
    }

    /**
     * Generate PDF
     */
    public function pdf(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['invoice.client', 'invoice.tenant', 'items']);
        $pdf = Pdf::loadView('pdf.delivery-note', compact('deliveryNote'));
        return $pdf->download("SuratJalan-{$deliveryNote->delivery_number}.pdf");
    }

    /**
     * Preview PDF
     */
    public function preview(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['invoice.client', 'invoice.tenant', 'items']);
        $pdf = Pdf::loadView('pdf.delivery-note', compact('deliveryNote'));
        return $pdf->stream("SuratJalan-{$deliveryNote->delivery_number}.pdf");
    }

    public function sendAuto(DeliveryNote $deliveryNote, \App\Services\NotificationService $notificationService)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->canUseWaGateway()) {
            return back()->with('error', 'Fitur Kirim Auto (WhatsApp) tidak tersedia di paket langganan Anda. Silakan upgrade paket untuk menggunakan fitur ini.');
        }

        // WhatsApp only for Delivery Notes usually
        $waEnabled = \App\Models\Setting::get('whatsapp_enabled', 'false') === 'true';
        $results = [];

        if ($waEnabled && $deliveryNote->invoice->client->phone) {
            try {
                $notificationService->notifyDeliveryNoteCreated($deliveryNote);
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

@extends('layouts.app')

@section('title', 'Detail Invoice')
@section('header', 'Detail Invoice')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Invoice Detail -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h2>
                    <p class="text-gray-500">{{ $invoice->subject ?? 'Invoice' }}</p>
                </div>
                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full 
                    @switch($invoice->status)
                        @case('draft') bg-gray-100 text-gray-800 @break
                        @case('sent') bg-blue-100 text-blue-800 @break
                        @case('partial') bg-yellow-100 text-yellow-800 @break
                        @case('paid') bg-green-100 text-green-800 @break
                        @case('overdue') bg-red-100 text-red-800 @break
                    @endswitch">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Client</p>
                    <p class="font-medium text-gray-900">{{ $invoice->client->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">{{ $invoice->client->address ?? '' }}</p>
                </div>
                <div class="text-right">
                    <div class="mb-2">
                        <span class="text-sm text-gray-500">Tanggal Invoice:</span>
                        <span class="font-medium ml-2">{{ $invoice->invoice_date->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Jatuh Tempo:</span>
                        <span class="font-medium ml-2 {{ $invoice->due_date < now() && $invoice->status != 'paid' ? 'text-red-600' : '' }}">
                            {{ $invoice->due_date->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $item->description }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">Subtotal</td>
                        <td class="px-6 py-3 text-right font-medium">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">Diskon</td>
                        <td class="px-6 py-3 text-right font-medium text-red-600">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">PPN</td>
                        <td class="px-6 py-3 text-right font-medium">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t-2">
                        <td colspan="3" class="px-6 py-3 text-right text-lg font-semibold text-gray-900">Total</td>
                        <td class="px-6 py-3 text-right text-lg font-bold text-primary-600">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes -->
        @if($invoice->notes || $invoice->terms)
        <div class="bg-white rounded-xl shadow-sm border p-6">
            @if($invoice->notes)
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-1">Catatan</h4>
                <p class="text-gray-600">{{ $invoice->notes }}</p>
            </div>
            @endif
            @if($invoice->terms)
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-1">Syarat & Ketentuan</h4>
                <p class="text-gray-600">{{ $invoice->terms }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Payment Summary -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pembayaran</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total</span>
                    <span class="font-medium">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Dibayar</span>
                    <span class="font-medium text-green-600">Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t">
                    <span class="text-gray-900 font-medium">Sisa</span>
                    <span class="font-bold text-red-600">Rp {{ number_format($invoice->amount_due, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($invoice->amount_due > 0 && $invoice->status !== 'draft')
            <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" 
               class="mt-4 w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Catat Pembayaran
            </a>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
            <div class="space-y-2">
                @if($invoice->status === 'draft' || $invoice->status === 'sent')
                <form action="{{ route('invoices.send', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Kirim Invoice
                    </button>
                </form>
                <a href="{{ route('invoices.edit', $invoice) }}" class="block w-full text-center px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Edit Invoice
                </a>
                @endif

                @if(auth()->user()->tenant && auth()->user()->tenant->canUseWaGateway())
                <form action="{{ route('invoices.send_auto', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100"
                        onclick="return confirm('Kirim notifikasi otomatis ke Client (WhatsApp)? Pastikan Anda memiliki kuota/paket yang sesuai.')">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        Kirim ke WhatsApp Client
                    </button>
                </form>
                @endif
                
                <a href="{{ route('invoices.preview', $invoice) }}" target="_blank" class="block w-full text-center px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Lihat PDF
                </a>
                <a href="{{ route('invoices.pdf', $invoice) }}" class="block w-full text-center px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Download PDF
                </a>
                
                <form action="{{ route('invoices.duplicate', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Duplikasi
                    </button>
                </form>

                <hr class="my-2">

                @if($invoice->amount_due > 0)
                <a href="{{ route('receipts.create', ['invoice_id' => $invoice->id]) }}" 
                   class="block w-full text-center px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Buat Kwitansi
                </a>
                @endif

                <a href="{{ route('delivery-notes.create', ['invoice_id' => $invoice->id]) }}" 
                   class="block w-full text-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Buat Surat Jalan
                </a>
            </div>
        </div>

        <!-- Payment History -->
        @if($invoice->payments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Pembayaran</h3>
            <div class="space-y-3">
                @foreach($invoice->payments as $payment)
                <div class="flex justify-between items-center py-2 border-b last:border-0">
                    <div>
                        <p class="text-sm font-medium">{{ $payment->payment_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->payment_method }}</p>
                    </div>
                    <span class="font-medium text-green-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

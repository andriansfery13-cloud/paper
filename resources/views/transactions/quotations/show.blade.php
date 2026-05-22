@extends('layouts.app')

@section('title', 'Detail Quotation')
@section('header', 'Detail Quotation')

@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Quotation Detail -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $quotation->quotation_number }}</h2>
                    <p class="text-gray-500">{{ $quotation->subject ?? 'Quotation' }}</p>
                </div>
                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full 
                    @switch($quotation->status)
                        @case('draft') bg-gray-100 text-gray-800 @break
                        @case('sent') bg-blue-100 text-blue-800 @break
                        @case('approved') bg-green-100 text-green-800 @break
                        @case('rejected') bg-red-100 text-red-800 @break
                        @case('expired') bg-yellow-100 text-yellow-800 @break
                        @case('converted') bg-purple-100 text-purple-800 @break
                    @endswitch">
                    {{ ucfirst($quotation->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Client</p>
                    <p class="font-medium text-gray-900">{{ $quotation->client->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">{{ $quotation->client->address ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $quotation->client->email ?? '' }}</p>
                </div>
                <div class="text-right">
                    <div class="mb-2">
                        <span class="text-sm text-gray-500">Tanggal Quotation:</span>
                        <span class="font-medium ml-2">{{ $quotation->quotation_date->format('d M Y') }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-sm text-gray-500">Berlaku Sampai:</span>
                        <span class="font-medium ml-2 {{ $quotation->valid_until < now() && !in_array($quotation->status, ['approved', 'converted']) ? 'text-red-600' : '' }}">
                            {{ $quotation->valid_until->format('d M Y') }}
                        </span>
                    </div>
                    @if($quotation->approved_at)
                    <div>
                        <span class="text-sm text-gray-500">Disetujui:</span>
                        <span class="font-medium ml-2 text-green-600">{{ $quotation->approved_at->format('d M Y') }}</span>
                    </div>
                    @endif
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
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pajak</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($quotation->items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $item->description }}</p>
                            @if($item->product)
                            <p class="text-xs text-gray-500">{{ $item->product->sku }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($item->tax_percent, 0) }}%</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($item->subtotal + $item->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm text-gray-600">Subtotal</td>
                        <td class="px-6 py-3 text-right font-medium">Rp {{ number_format($quotation->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm text-gray-600">PPN</td>
                        <td class="px-6 py-3 text-right font-medium">Rp {{ number_format($quotation->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    @if($quotation->discount_amount > 0)
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm text-gray-600">Diskon</td>
                        <td class="px-6 py-3 text-right font-medium text-red-600">- Rp {{ number_format($quotation->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="border-t-2">
                        <td colspan="4" class="px-6 py-3 text-right text-lg font-semibold text-gray-900">Total</td>
                        <td class="px-6 py-3 text-right text-lg font-bold text-primary-600">Rp {{ number_format($quotation->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes -->
        @if($quotation->notes || $quotation->terms)
        <div class="bg-white rounded-xl shadow-sm border p-6">
            @if($quotation->notes)
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-1">Catatan</h4>
                <p class="text-gray-600">{{ $quotation->notes }}</p>
            </div>
            @endif
            @if($quotation->terms)
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-1">Syarat & Ketentuan</h4>
                <p class="text-gray-600">{{ $quotation->terms }}</p>
            </div>
            @endif
        </div>
        @endif

        <!-- Rejection Reason -->
        @if($quotation->status === 'rejected' && $quotation->rejection_reason)
        <div class="bg-red-50 rounded-xl border border-red-200 p-6">
            <h4 class="text-sm font-medium text-red-800 mb-1">Alasan Penolakan</h4>
            <p class="text-red-700">{{ $quotation->rejection_reason }}</p>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Summary -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total</span>
                    <span class="font-bold text-primary-600">{{ $quotation->formatted_total }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="font-medium capitalize">{{ $quotation->status }}</span>
                </div>
                @if($quotation->creator)
                <div class="flex justify-between">
                    <span class="text-gray-600">Dibuat oleh</span>
                    <span class="font-medium">{{ $quotation->creator->name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
            <div class="space-y-2">
                @if($quotation->status === 'draft')
                <form action="{{ route('quotations.send', $quotation) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Kirim Quotation
                    </button>
                </form>
                <a href="{{ route('quotations.edit', $quotation) }}" class="block w-full text-center px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Edit Quotation
                </a>
                @endif

                @if(in_array($quotation->status, ['draft', 'sent']))
                <form action="{{ route('quotations.approve', $quotation) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Approve
                    </button>
                </form>
                
                <div x-data="{ showReject: false }">
                    <button @click="showReject = !showReject" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Reject
                    </button>
                    <form action="{{ route('quotations.reject', $quotation) }}" method="POST" x-show="showReject" x-cloak class="mt-2">
                        @csrf
                        <textarea name="rejection_reason" rows="2" 
                            class="w-full px-3 py-2 border rounded-lg text-sm mb-2"
                            placeholder="Alasan penolakan (opsional)"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                            Konfirmasi Reject
                        </button>
                    </form>
                </div>
                @endif

                @if($quotation->canBeConverted())
                <form action="{{ route('quotations.convert', $quotation) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Convert ke Invoice
                    </button>
                </form>
                @endif
                
                <a href="{{ route('quotations.preview', $quotation) }}" target="_blank" class="block w-full text-center px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Lihat PDF
                </a>
                <a href="{{ route('quotations.pdf', $quotation) }}" class="block w-full text-center px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Download PDF
                </a>

                @if(auth()->user()->tenant && auth()->user()->tenant->canUseWaGateway())
                <form action="{{ route('quotations.send_auto', $quotation) }}" method="POST">
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

                @if($quotation->status === 'draft')
                <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" 
                    onsubmit="return confirm('Yakin ingin menghapus quotation ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 text-red-600 border border-red-200 rounded-lg hover:bg-red-50">
                        Hapus Quotation
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Related Invoices -->
        @if($quotation->invoices->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Invoice Terkait</h3>
            <div class="space-y-2">
                @foreach($quotation->invoices as $invoice)
                <a href="{{ route('invoices.show', $invoice) }}" 
                    class="flex justify-between items-center p-3 border rounded-lg hover:bg-gray-50">
                    <div>
                        <p class="font-medium text-primary-600">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs text-gray-500">{{ $invoice->invoice_date->format('d M Y') }}</p>
                    </div>
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                        @switch($invoice->status)
                            @case('draft') bg-gray-100 text-gray-800 @break
                            @case('sent') bg-blue-100 text-blue-800 @break
                            @case('paid') bg-green-100 text-green-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Verification -->
        <!-- Verification -->
        @if($quotation->verification_code)
        <div class="bg-gray-50 rounded-xl border p-6">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Verifikasi Dokumen</h4>
            <div class="flex flex-col items-center justify-center space-y-3">
                 <!-- QR Code -->
                 <div class="bg-white p-2 rounded shadow-sm">
                            <div class="visible-print text-center">
                                {!! QrCode::format('svg')->size(100)->generate($quotation->verification_url) !!}
                                <p class="mt-2 text-xs text-gray-500">Scan untuk Verifikasi</p>
                            </div>
                </div>
                
                <div class="text-center w-full">
                    <p class="text-xs text-gray-500 mb-1">Kode Verifikasi:</p>
                    <p class="font-mono text-sm font-bold text-gray-900 break-all select-all bg-white px-2 py-1 rounded border">{{ $quotation->verification_code }}</p>
                </div>
                
                <a href="{{ route('verify.quotation', $quotation->verification_code) }}" target="_blank" 
                    class="w-full text-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Buka Halaman Validasi
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

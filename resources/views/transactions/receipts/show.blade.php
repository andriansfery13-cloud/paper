@extends('layouts.app')

@section('title', 'Detail Kwitansi')
@section('header', 'Detail Kwitansi')

@section('content')
<div class="grid grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="col-span-2 space-y-6">
        <!-- Receipt Card -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $receipt->receipt_number }}</h2>
                    <p class="text-gray-500">Tanggal: {{ $receipt->receipt_date->format('d F Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Jumlah</p>
                    <p class="text-3xl font-bold text-green-600">{{ $receipt->formatted_amount }}</p>
                </div>
            </div>

            <hr class="my-4">

            <!-- Client Info -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Diterima Dari</h4>
                    <p class="font-medium text-gray-900">{{ $receipt->invoice->client->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">{{ $receipt->invoice->client->address ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $receipt->invoice->client->email ?? '' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Untuk Invoice</h4>
                    <a href="{{ route('invoices.show', $receipt->invoice) }}" class="font-medium text-primary-600 hover:text-primary-700">
                        {{ $receipt->invoice->invoice_number }}
                    </a>
                    <p class="text-sm text-gray-600">Total: {{ $receipt->invoice->formatted_total }}</p>
                    <p class="text-sm text-gray-600">
                        Status: 
                        <span class="inline-flex px-2 py-0.5 text-xs rounded-full 
                            {{ $receipt->invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $receipt->invoice->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Detail Pembayaran</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Metode Pembayaran</p>
                        <p class="font-medium">
                            @switch($receipt->payment->payment_method ?? 'cash')
                                @case('cash') Tunai @break
                                @case('transfer') Transfer Bank @break
                                @case('check') Cek/Giro @break
                                @case('qris') QRIS @break
                                @default {{ ucfirst($receipt->payment->payment_method ?? 'Lainnya') }}
                            @endswitch
                        </p>
                    </div>
                    @if($receipt->payment && $receipt->payment->reference_number)
                    <div>
                        <p class="text-sm text-gray-500">No. Referensi</p>
                        <p class="font-medium">{{ $receipt->payment->reference_number }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Dibuat Oleh</p>
                        <p class="font-medium">{{ $receipt->creator->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Dibuat</p>
                        <p class="font-medium">{{ $receipt->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            @if($receipt->notes)
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Catatan</h4>
                <p class="text-gray-600">{{ $receipt->notes }}</p>
            </div>
            @endif

            @if($receipt->verification_code)
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-gray-400 text-center">
                    Kode Verifikasi: {{ $receipt->verification_code }}
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar Actions -->
    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="space-y-2">
                    @if(auth()->user()->tenant && auth()->user()->tenant->canUseWaGateway())
                    <form action="{{ route('receipts.send_auto', $receipt) }}" method="POST">
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



                <a href="{{ route('receipts.preview', $receipt) }}" target="_blank"
                    class="flex items-center w-full px-4 py-2 text-left bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Kwitansi
                </a>
                <a href="{{ route('receipts.pdf', $receipt) }}"
                    class="flex items-center w-full px-4 py-2 text-left bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download PDF
                </a>
                <hr class="my-2">
                <a href="{{ route('invoices.show', $receipt->invoice) }}"
                    class="flex items-center w-full px-4 py-2 text-left bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Lihat Invoice
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-4">
            <h4 class="font-medium text-gray-900 mb-3">Hapus Kwitansi</h4>
            <p class="text-sm text-gray-500 mb-3">Menghapus kwitansi akan membatalkan pembayaran terkait.</p>
            <form action="{{ route('receipts.destroy', $receipt) }}" method="POST" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kwitansi ini? Pembayaran juga akan dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors">
                    Hapus Kwitansi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

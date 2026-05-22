@extends('layouts.app')

@section('title', 'Detail Surat Jalan')
@section('header', 'Detail Surat Jalan')

@section('content')
    <div class="grid grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="col-span-2 space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $deliveryNote->delivery_number }}</h2>
                        <p class="text-gray-500">{{ $deliveryNote->delivery_date->format('d F Y') }}</p>
                    </div>
                    <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full
                                    @if($deliveryNote->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($deliveryNote->status == 'in_transit') bg-blue-100 text-blue-800
                                    @elseif($deliveryNote->status == 'delivered') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                        {{ $deliveryNote->status_label }}
                    </span>
                </div>

                <hr class="my-4">

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Penerima</h4>
                        <p class="font-medium text-gray-900">{{ $deliveryNote->recipient_name }}</p>
                        <p class="text-sm text-gray-600">{{ $deliveryNote->recipient_address ?? '-' }}</p>
                        @if($deliveryNote->recipient_phone)
                            <p class="text-sm text-gray-600">Telp: {{ $deliveryNote->recipient_phone }}</p>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Dari Invoice</h4>
                        <a href="{{ route('invoices.show', $deliveryNote->invoice) }}"
                            class="font-medium text-primary-600 hover:text-primary-700">
                            {{ $deliveryNote->invoice->invoice_number }}
                        </a>
                        <p class="text-sm text-gray-600">Client: {{ $deliveryNote->invoice->client->name ?? '-' }}</p>
                    </div>
                </div>

                @if($deliveryNote->driver_name || $deliveryNote->vehicle_number)
                    <div class="mt-4 pt-4 border-t">
                        <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Informasi Pengiriman</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @if($deliveryNote->driver_name)
                                <div><span class="text-gray-500">Supir:</span> {{ $deliveryNote->driver_name }}</div>
                            @endif
                            @if($deliveryNote->vehicle_number)
                                <div><span class="text-gray-500">Kendaraan:</span> {{ $deliveryNote->vehicle_number }}</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Items -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Daftar Barang</h3>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($deliveryNote->items as $index => $item)
                            <tr>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900">{{ $item->description }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900 text-center">{{ $item->quantity }} {{ $item->unit }}
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $item->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($deliveryNote->notes)
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Catatan</h4>
                    <p class="text-gray-600">{{ $deliveryNote->notes }}</p>
                </div>
            @endif

            @if($deliveryNote->status === 'delivered')
                <div class="bg-green-50 rounded-xl border border-green-200 p-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-green-800">Barang Telah Diterima</h4>
                            <p class="text-sm text-green-700 mt-1">
                                Diterima oleh: <strong>{{ $deliveryNote->received_by_name }}</strong><br>
                                Pada: {{ $deliveryNote->delivered_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border p-4">
                <h4 class="font-medium text-gray-900 mb-3">Aksi</h4>
                <div class="space-y-2">
                    @if(auth()->user()->tenant && auth()->user()->tenant->canUseWaGateway())
                        <form action="{{ route('delivery-notes.send_auto', $deliveryNote) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100"
                                onclick="return confirm('Kirim notifikasi otomatis ke Client (WhatsApp)? Pastikan Anda memiliki kuota/paket yang sesuai.')">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                                Kirim ke WhatsApp Client
                            </button>
                        </form>
                    @endif


                    <a href="{{ route('delivery-notes.preview', $deliveryNote) }}" target="_blank"
                        class="flex items-center w-full px-4 py-2 text-left bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        Cetak Surat Jalan
                    </a>
                    <a href="{{ route('delivery-notes.pdf', $deliveryNote) }}"
                        class="flex items-center w-full px-4 py-2 text-left bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>

            @if($deliveryNote->status !== 'delivered' && $deliveryNote->status !== 'cancelled')
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Update Status</h4>
                    <div class="space-y-2">
                        @if($deliveryNote->status === 'pending')
                            <form action="{{ route('delivery-notes.in-transit', $deliveryNote) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100">
                                    Tandai Dalam Perjalanan
                                </button>
                            </form>
                        @endif

                        @if($deliveryNote->status === 'pending' || $deliveryNote->status === 'in_transit')
                            <form action="{{ route('delivery-notes.delivered', $deliveryNote) }}" method="POST"
                                x-data="{ showReceiver: false }">
                                @csrf
                                <div x-show="!showReceiver">
                                    <button type="button" @click="showReceiver = true"
                                        class="w-full px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100">
                                        Konfirmasi Terkirim
                                    </button>
                                </div>
                                <div x-show="showReceiver" class="space-y-2">
                                    <input type="text" name="received_by_name" placeholder="Nama Penerima" required
                                        class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        @endif

                        <form action="{{ route('delivery-notes.cancel', $deliveryNote) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin membatalkan surat jalan ini?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100">
                                Batalkan
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($deliveryNote->status !== 'delivered')
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Lainnya</h4>
                    <div class="space-y-2">
                        <a href="{{ route('delivery-notes.edit', $deliveryNote) }}"
                            class="flex items-center w-full px-4 py-2 text-left bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('delivery-notes.destroy', $deliveryNote) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus surat jalan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center w-full px-4 py-2 text-left bg-red-50 text-red-700 rounded-lg hover:bg-red-100">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
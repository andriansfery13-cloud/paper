@extends('layouts.app')

@section('title', 'Surat Jalan')
@section('header', 'Surat Jalan')

@section('content')
    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg p-4 border">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 border">
            <p class="text-sm text-gray-500">Menunggu</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 border">
            <p class="text-sm text-gray-500">Dalam Perjalanan</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['in_transit'] }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 border">
            <p class="text-sm text-gray-500">Terkirim</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['delivered'] }}</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border mb-6">
        <div class="p-4 flex flex-wrap items-center justify-between gap-4">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari surat jalan..."
                    class="px-4 py-2 border rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                <select name="status"
                    class="px-4 py-2 border rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>Dalam Perjalanan
                    </option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Terkirim</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>

            <a href="{{ route('invoices.index') }}"
                class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat dari Invoice
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Surat Jalan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($deliveryNotes as $note)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('delivery-notes.show', $note) }}"
                                class="text-primary-600 font-medium hover:text-primary-700">
                                {{ $note->delivery_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('invoices.show', $note->invoice) }}" class="text-gray-600 hover:text-primary-600">
                                {{ $note->invoice->invoice_number ?? '-' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $note->recipient_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $note->delivery_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                @if($note->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($note->status == 'in_transit') bg-blue-100 text-blue-800
                                @elseif($note->status == 'delivered') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $note->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('delivery-notes.show', $note) }}"
                                    class="p-1 text-gray-500 hover:text-primary-600" title="Lihat">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </a>
                                <a href="{{ route('delivery-notes.preview', $note) }}" target="_blank"
                                    class="p-1 text-gray-500 hover:text-primary-600" title="Cetak">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                </path>
                            </svg>
                            <p>Belum ada surat jalan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($deliveryNotes->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $deliveryNotes->links() }}
            </div>
        @endif
    </div>
@endsection
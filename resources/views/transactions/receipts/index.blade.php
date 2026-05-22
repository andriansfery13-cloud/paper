@extends('layouts.app')

@section('title', 'Kwitansi')
@section('header', 'Kwitansi')

@section('content')
    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg p-4 border">
            <p class="text-sm text-gray-500">Total Kwitansi</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 border">
            <p class="text-sm text-gray-500">Bulan Ini</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['this_month'] }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 border">
            <p class="text-sm text-gray-500">Total Nominal</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm border mb-6">
        <div class="p-4 flex flex-wrap items-center justify-between gap-4">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kwitansi..."
                    class="px-4 py-2 border rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
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

    <!-- Receipts Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Kwitansi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($receipts as $receipt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('receipts.show', $receipt) }}"
                                class="text-primary-600 font-medium hover:text-primary-700">
                                {{ $receipt->receipt_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('invoices.show', $receipt->invoice) }}"
                                class="text-gray-600 hover:text-primary-600">
                                {{ $receipt->invoice->invoice_number ?? '-' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $receipt->invoice->client->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $receipt->receipt_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                            {{ $receipt->formatted_amount }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('receipts.show', $receipt) }}"
                                    class="p-1 text-gray-500 hover:text-primary-600" title="Lihat">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </a>
                                <a href="{{ route('receipts.preview', $receipt) }}" target="_blank"
                                    class="p-1 text-gray-500 hover:text-primary-600" title="Cetak">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                </a>
                                <a href="{{ route('receipts.pdf', $receipt) }}" class="p-1 text-gray-500 hover:text-primary-600"
                                    title="Download">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p>Belum ada kwitansi</p>
                            <a href="{{ route('invoices.index') }}"
                                class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                                Buat Kwitansi dari Invoice
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($receipts->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
@endsection
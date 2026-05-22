@extends('layouts.app')

@section('title', 'Pembayaran')
@section('header', 'Data Pembayaran')

@section('content')
    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pembayaran..."
                    class="px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                <select name="method" class="px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Metode</option>
                    <option value="transfer" {{ request('method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="qris" {{ request('method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Filter</button>
            </form>
        </div>
        <a href="{{ route('payments.create') }}"
            class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Catat Pembayaran
        </a>
    </div>

    <!-- Payment Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pembayaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('payments.show', $payment) }}"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                    {{ $payment->payment_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <a href="{{ route('invoices.show', $payment->invoice) }}" class="hover:text-primary-600">
                                    {{ $payment->invoice->invoice_number ?? '-' }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $payment->invoice->client->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $payment->payment_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800">
                                    {{ ucfirst($payment->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-green-600">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $payment->status === 'success' ? 'bg-green-100 text-green-800' :
                    ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <p>Belum ada pembayaran</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($payments->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
@endsection
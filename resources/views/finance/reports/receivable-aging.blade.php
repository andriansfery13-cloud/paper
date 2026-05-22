@extends('layouts.app')

@section('title', 'Laporan Umur Piutang')
@section('header', 'Laporan Umur Piutang')

@section('content')
    <div class="space-y-6">
        <!-- Summary -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-blue-100">Total Piutang</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalReceivables, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('finance.reports.profit-loss') }}"
                    class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                    ← Kembali ke Laba Rugi
                </a>
            </div>
        </div>

        <!-- Aging Buckets -->
        <div class="grid grid-cols-5 gap-4">
            <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
                <p class="text-sm text-gray-500">Belum Jatuh Tempo</p>
                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($aging['current']['total'], 0, ',', '.') }}
                </p>
                <p class="text-sm text-gray-400">{{ $aging['current']['invoices']->count() }} invoice</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
                <p class="text-sm text-gray-500">1-30 Hari</p>
                <p class="text-2xl font-bold text-yellow-600">Rp {{ number_format($aging['1_30']['total'], 0, ',', '.') }}
                </p>
                <p class="text-sm text-gray-400">{{ $aging['1_30']['invoices']->count() }} invoice</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
                <p class="text-sm text-gray-500">31-60 Hari</p>
                <p class="text-2xl font-bold text-orange-600">Rp {{ number_format($aging['31_60']['total'], 0, ',', '.') }}
                </p>
                <p class="text-sm text-gray-400">{{ $aging['31_60']['invoices']->count() }} invoice</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
                <p class="text-sm text-gray-500">61-90 Hari</p>
                <p class="text-2xl font-bold text-red-600">Rp {{ number_format($aging['61_90']['total'], 0, ',', '.') }}</p>
                <p class="text-sm text-gray-400">{{ $aging['61_90']['invoices']->count() }} invoice</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
                <p class="text-sm text-gray-500">&gt;90 Hari</p>
                <p class="text-2xl font-bold text-red-700">Rp {{ number_format($aging['over_90']['total'], 0, ',', '.') }}
                </p>
                <p class="text-sm text-gray-400">{{ $aging['over_90']['invoices']->count() }} invoice</p>
            </div>
        </div>

        @foreach(['current' => 'Belum Jatuh Tempo', '1_30' => '1-30 Hari', '31_60' => '31-60 Hari', '61_90' => '61-90 Hari', 'over_90' => '>90 Hari'] as $key => $label)
            @if($aging[$key]['invoices']->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="p-4 border-b bg-gray-50">
                        <h4 class="font-semibold text-gray-900">{{ $label }} - Rp
                            {{ number_format($aging[$key]['total'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Klien</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sisa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($aging[$key]['invoices'] as $invoice)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-sm">
                                            <a href="{{ route('invoices.show', $invoice) }}"
                                                class="text-primary-600 hover:underline">{{ $invoice->invoice_number }}</a>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-900">{{ $invoice->client->name ?? '-' }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">{{ $invoice->invoice_date->format('d M Y') }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">{{ $invoice->due_date->format('d M Y') }}</td>
                                        <td class="px-6 py-3 text-sm text-right font-medium text-blue-600">
                                            {{ $invoice->formatted_amount_due }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endsection
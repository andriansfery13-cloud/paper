@extends('layouts.app')

@section('title', 'Detail Piutang - ' . $client->name)
@section('header', 'Detail Piutang')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Client Info -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 flex items-center">
                <div
                    class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 text-xl font-bold">
                    {{ strtoupper(substr($client->name, 0, 2)) }}
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-semibold text-gray-900">{{ $client->name }}</h3>
                    @if($client->company)
                        <p class="text-gray-500">{{ $client->company }}</p>
                    @endif
                    <div class="flex gap-4 mt-2 text-sm text-gray-500">
                        @if($client->email)
                            <span>{{ $client->email }}</span>
                        @endif
                        @if($client->phone)
                            <span>{{ $client->phone }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Aging Summary -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Analisis Umur Piutang</h4>
            <div class="grid grid-cols-5 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Belum Jatuh Tempo</p>
                    <p class="text-lg font-bold text-green-600">Rp {{ number_format($aging['current'], 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">1-30 Hari</p>
                    <p class="text-lg font-bold text-yellow-600">Rp {{ number_format($aging['1_30'], 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <p class="text-sm text-gray-600">31-60 Hari</p>
                    <p class="text-lg font-bold text-orange-600">Rp {{ number_format($aging['31_60'], 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600">61-90 Hari</p>
                    <p class="text-lg font-bold text-red-600">Rp {{ number_format($aging['61_90'], 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-4 bg-red-100 rounded-lg">
                    <p class="text-sm text-gray-600">&gt;90 Hari</p>
                    <p class="text-lg font-bold text-red-700">Rp {{ number_format($aging['over_90'], 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="mt-4 text-right">
                <span class="text-gray-600">Total Piutang:</span>
                <span class="text-2xl font-bold text-blue-600 ml-2">Rp
                    {{ number_format($totalOutstanding, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Unpaid Invoices -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 border-b">
                <h4 class="text-lg font-semibold text-gray-900">Invoice Belum Lunas</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Invoice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sisa</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $invoice->invoice_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $invoice->invoice_date->format('d M Y') }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm {{ $invoice->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                    {{ $invoice->due_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">
                                    {{ $invoice->formatted_total }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-blue-600">
                                    {{ $invoice->formatted_amount_due }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $invoice->status_badge }}-100 text-{{ $invoice->status_badge }}-800">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('invoices.show', $invoice) }}"
                                        class="text-primary-600 hover:text-primary-900">
                                        Lihat →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada invoice belum lunas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-start">
            <a href="{{ route('finance.receivables.index') }}"
                class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors">
                &larr; Kembali
            </a>
        </div>
    </div>
@endsection
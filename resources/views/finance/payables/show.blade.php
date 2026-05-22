@extends('layouts.app')

@section('title', 'Detail Hutang')
@section('header', 'Detail Hutang')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Main Info Card -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 border-b flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $payable->reference_number }}</h3>
                    <p class="text-sm text-gray-500">{{ $payable->supplier->name ?? '-' }}</p>
                </div>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $payable->status_badge }}-100 text-{{ $payable->status_badge }}-800">
                    {{ $payable->status_label }}
                </span>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-3 gap-6 text-center">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Total Hutang</p>
                        <p class="text-xl font-bold text-gray-900">{{ $payable->formatted_amount }}</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-green-600">Sudah Dibayar</p>
                        <p class="text-xl font-bold text-green-700">{{ $payable->formatted_amount_paid }}</p>
                    </div>
                    <div class="p-4 bg-orange-50 rounded-lg">
                        <p class="text-sm text-orange-600">Sisa</p>
                        <p class="text-xl font-bold text-orange-700">{{ $payable->formatted_amount_due }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tanggal Transaksi</label>
                        <p class="mt-1 text-gray-900">{{ $payable->transaction_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jatuh Tempo</label>
                        <p class="mt-1 {{ $payable->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                            {{ $payable->due_date->format('d F Y') }}
                            @if($payable->isOverdue())
                                <span class="text-sm">(Lewat jatuh tempo)</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Deskripsi</label>
                    <p class="mt-1 text-gray-900">{{ $payable->description }}</p>
                </div>

                @if($payable->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Catatan</label>
                        <p class="mt-1 text-gray-900">{{ $payable->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Record Payment Card -->
        @if($payable->amount_due > 0)
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-6 border-b">
                    <h4 class="text-lg font-semibold text-gray-900">Catat Pembayaran</h4>
                </div>
                <form action="{{ route('finance.payables.payment', $payable) }}" method="POST">
                    @csrf
                    <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="number" name="amount" required min="1" max="{{ $payable->amount_due }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode *</label>
                            <select name="payment_method" required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih</option>
                                <option value="cash">Tunai</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                            <input type="text" name="reference_number"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Opsional">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Bayar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- Payment History -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 border-b">
                <h4 class="text-lg font-semibold text-gray-900">Riwayat Pembayaran</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referensi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($payable->payments as $payment)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payment_method_label }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->reference_number ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-green-600">
                                    {{ $payment->formatted_amount }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->creator->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada pembayaran
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-between">
            <a href="{{ route('finance.payables.index') }}"
                class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors">
                &larr; Kembali
            </a>
            @if(!$payable->payments()->exists())
                <form action="{{ route('finance.payables.destroy', $payable) }}" method="POST"
                    onsubmit="return confirm('Hapus hutang ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
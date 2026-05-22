@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')
@section('header', 'Laporan Laba Rugi')

@section('content')
    <div class="space-y-6">
        <!-- Header with Date Filter -->
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <form action="{{ route('finance.reports.profit-loss') }}" method="GET" class="flex items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                        class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                        class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Filter</button>
                <a href="{{ route('finance.reports.profit-loss') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-50">Reset</a>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                <p class="text-green-100">Total Pemasukan</p>
                <p class="text-3xl font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-6 text-white">
                <p class="text-red-100">Total Pengeluaran</p>
                <p class="text-3xl font-bold">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
            </div>
            <div
                class="bg-gradient-to-r {{ $netProfit >= 0 ? 'from-blue-500 to-blue-600' : 'from-orange-500 to-orange-600' }} rounded-xl p-6 text-white">
                <p class="{{ $netProfit >= 0 ? 'text-blue-100' : 'text-orange-100' }}">
                    {{ $netProfit >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</p>
                <p class="text-3xl font-bold">Rp {{ number_format(abs($netProfit), 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Chart and Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Trend Chart -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Tren Bulanan</h4>
                <canvas id="monthlyChart" height="200"></canvas>
            </div>

            <!-- Income Breakdown -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Pemasukan per Sumber</h4>
                <div class="space-y-3">
                    @php
                        $sourceLabels = ['invoice_payment' => 'Pembayaran Invoice', 'manual' => 'Input Manual', 'other' => 'Lainnya'];
                    @endphp
                    @forelse($incomeBySource as $source => $amount)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-700">{{ $sourceLabels[$source] ?? $source }}</span>
                            <span class="font-medium text-green-600">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada pemasukan</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Expense Breakdown -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Pengeluaran per Kategori</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($expensesByCategory as $expense)
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">{{ $expense['category'] }}</span>
                        <span class="font-medium text-red-600">Rp {{ number_format($expense['total'], 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4 col-span-full">Tidak ada pengeluaran</p>
                @endforelse
            </div>
        </div>

        <!-- Other Reports Link -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Laporan Lainnya</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('finance.reports.receivable-aging') }}"
                    class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <h5 class="font-medium text-gray-900">Umur Piutang</h5>
                    <p class="text-sm text-gray-500">Analisis umur piutang klien</p>
                </a>
                <a href="{{ route('finance.reports.payable-aging') }}"
                    class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <h5 class="font-medium text-gray-900">Umur Hutang</h5>
                    <p class="text-sm text-gray-500">Analisis umur hutang supplier</p>
                </a>
                <a href="{{ route('finance.reports.cash-flow') }}"
                    class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <h5 class="font-medium text-gray-900">Arus Kas</h5>
                    <p class="text-sm text-gray-500">Laporan arus kas masuk dan keluar</p>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('monthlyChart').getContext('2d');
                const monthlyData = @json($monthlyData);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: monthlyData.map(d => d.month),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: monthlyData.map(d => d.income),
                                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                                borderColor: 'rgb(34, 197, 94)',
                                borderWidth: 1
                            },
                            {
                                label: 'Pengeluaran',
                                data: monthlyData.map(d => d.expense),
                                backgroundColor: 'rgba(239, 68, 68, 0.5)',
                                borderColor: 'rgb(239, 68, 68)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
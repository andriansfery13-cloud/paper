@extends('layouts.app')

@section('title', 'Laporan Arus Kas')
@section('header', 'Laporan Arus Kas')

@section('content')
    <div class="space-y-6">
        <!-- Header with Date Filter -->
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <form action="{{ route('finance.reports.cash-flow') }}" method="GET" class="flex items-end gap-4">
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
                <a href="{{ route('finance.reports.cash-flow') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-50">Reset</a>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                <p class="text-green-100">Kas Masuk</p>
                <p class="text-3xl font-bold">Rp {{ number_format($totalCashIn, 0, ',', '.') }}</p>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-6 text-white">
                <p class="text-red-100">Kas Keluar</p>
                <p class="text-3xl font-bold">Rp {{ number_format($totalCashOut, 0, ',', '.') }}</p>
            </div>
            <div
                class="bg-gradient-to-r {{ $netCashFlow >= 0 ? 'from-blue-500 to-blue-600' : 'from-orange-500 to-orange-600' }} rounded-xl p-6 text-white">
                <p class="{{ $netCashFlow >= 0 ? 'text-blue-100' : 'text-orange-100' }}">Arus Kas Bersih</p>
                <p class="text-3xl font-bold">Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Grafik Arus Kas Harian</h4>
            <canvas id="cashFlowChart" height="120"></canvas>
        </div>

        <!-- Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cash In by Method -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Kas Masuk per Metode</h4>
                <div class="space-y-3">
                    @php
                        $methodLabels = ['cash' => 'Tunai', 'bank_transfer' => 'Transfer Bank', 'credit_card' => 'Kartu Kredit', 'qris' => 'QRIS', 'ewallet' => 'E-Wallet', 'va' => 'Virtual Account'];
                    @endphp
                    @forelse($cashIn as $method => $amount)
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-gray-700">{{ $methodLabels[$method] ?? $method ?? 'Lainnya' }}</span>
                            <span class="font-medium text-green-600">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada kas masuk</p>
                    @endforelse
                </div>
            </div>

            <!-- Cash Out by Method -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Kas Keluar per Metode</h4>
                <div class="space-y-3">
                    @forelse($cashOut as $method => $amount)
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <span class="text-gray-700">{{ $methodLabels[$method] ?? $method ?? 'Lainnya' }}</span>
                            <span class="font-medium text-red-600">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada kas keluar</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('finance.reports.profit-loss') }}"
                class="px-4 py-2 border rounded-lg hover:bg-gray-50 inline-block">
                ← Kembali ke Laba Rugi
            </a>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('cashFlowChart').getContext('2d');
                const dailyData = @json($dailyCashFlow);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dailyData.map(d => d.date),
                        datasets: [
                            {
                                label: 'Kas Masuk',
                                data: dailyData.map(d => d.cash_in),
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Kas Keluar',
                                data: dailyData.map(d => d.cash_out),
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: true,
                                tension: 0.3
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
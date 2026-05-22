@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pendapatan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp
                        {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}
                    </p>
                    <div class="flex items-center mt-2">
                        @if($stats['revenue_growth'] >= 0)
                            <span class="text-green-500 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $stats['revenue_growth'] }}%
                            </span>
                        @else
                            <span class="text-red-500 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ abs($stats['revenue_growth']) }}%
                            </span>
                        @endif
                        <span class="text-gray-400 text-sm ml-2">vs bulan lalu</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Outstanding -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Piutang</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp
                        {{ number_format($stats['total_outstanding'], 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-400 mt-2">{{ $stats['unpaid_invoices'] }} invoice belum lunas</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Invoices -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Invoice</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_invoices'] }}</p>
                    <p class="text-sm text-red-500 mt-2">{{ $stats['overdue_invoices'] }} overdue</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Clients -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Client</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_clients'] }}</p>
                    <p class="text-sm text-green-500 mt-2">+{{ $stats['new_clients'] }} bulan ini</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotation Conversion Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-sm p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Total Penawaran</p>
                    <p class="text-2xl font-bold">{{ $conversionStats['total'] }}</p>
                </div>
                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-sm p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Disetujui</p>
                    <p class="text-2xl font-bold">{{ $conversionStats['approved'] + $conversionStats['converted'] }}</p>
                </div>
                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl shadow-sm p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Conversion Rate</p>
                    <p class="text-2xl font-bold">{{ $conversionStats['conversion_rate'] }}%</p>
                </div>
                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-xl shadow-sm p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Approval Rate</p>
                    <p class="text-2xl font-bold">{{ $conversionStats['approval_rate'] }}%</p>
                </div>
                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Grafik Pendapatan</h3>
                <select class="text-sm border-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option>6 Bulan Terakhir</option>
                    <option>12 Bulan Terakhir</option>
                </select>
            </div>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="{{ route('invoices.create') }}"
                    class="flex items-center p-3 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Buat Invoice Baru</p>
                        <p class="text-xs text-gray-500">Buat tagihan untuk client</p>
                    </div>
                </a>

                <a href="{{ route('clients.create') }}"
                    class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Tambah Client</p>
                        <p class="text-xs text-gray-500">Daftarkan client baru</p>
                    </div>
                </a>

                <a href="{{ route('payments.create') }}"
                    class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Catat Pembayaran</p>
                        <p class="text-xs text-gray-500">Input pembayaran masuk</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Top Clients & Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Top Clients -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Top Client</h3>
                    <a href="{{ route('clients.index') }}" class="text-sm text-primary-600 hover:text-primary-700">Lihat
                        Semua</a>
                </div>
            </div>
            <div class="divide-y">
                @forelse($topClients as $index => $client)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white text-sm font-bold mr-3">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $client->name }}</p>
                                <p class="text-xs text-gray-500">{{ $client->company ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-green-600">Rp
                                {{ number_format($client->total_revenue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        Belum ada data client
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
                    <a href="{{ route('products.index') }}" class="text-sm text-primary-600 hover:text-primary-700">Lihat
                        Semua</a>
                </div>
            </div>
            <div class="divide-y">
                @forelse($topProducts as $index => $product)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white text-sm font-bold mr-3">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->code }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ number_format($product->total_sold, 0, ',', '.') }} terjual</p>
                            <p class="text-xs text-gray-500">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        Belum ada data produk
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Invoices & Overdue -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Recent Invoices -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Invoice Terbaru</h3>
                    <a href="{{ route('invoices.index') }}" class="text-sm text-primary-600 hover:text-primary-700">Lihat
                        Semua</a>
                </div>
            </div>
            <div class="divide-y">
                @forelse($recentInvoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}"
                        class="flex items-center justify-between p-4 hover:bg-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                            <p class="text-xs text-gray-500">{{ $invoice->client->name ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->formatted_total }}</p>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $invoice->status_badge }}-100 text-{{ $invoice->status_badge }}-800">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        Belum ada invoice
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Invoice Jatuh Tempo</h3>
                    @if(count($overdueInvoices) > 0)
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ count($overdueInvoices) }} overdue
                        </span>
                    @endif
                </div>
            </div>
            <div class="divide-y">
                @forelse($overdueInvoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}"
                        class="flex items-center justify-between p-4 hover:bg-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                            <p class="text-xs text-gray-500">{{ $invoice->client->name ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-red-600">{{ $invoice->formatted_amount_due }}</p>
                            <p class="text-xs text-red-500">Jatuh tempo {{ $invoice->due_date->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tidak ada invoice jatuh tempo
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="mt-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                    <a href="{{ route('settings.company') }}" class="text-sm text-primary-600 hover:text-primary-700">Lihat
                        Semua</a>
                </div>
            </div>
            <div class="p-6">
                @if($recentActivities->count() > 0)
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($recentActivities as $index => $activity)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                @php
                                                    $actionColors = [
                                                        'created' => 'bg-green-500',
                                                        'updated' => 'bg-blue-500',
                                                        'deleted' => 'bg-red-500',
                                                        'sent' => 'bg-indigo-500',
                                                        'approved' => 'bg-emerald-500',
                                                        'rejected' => 'bg-rose-500',
                                                    ];
                                                    $bgColor = $actionColors[$activity->action] ?? 'bg-gray-400';
                                                @endphp
                                                <span
                                                    class="h-8 w-8 rounded-full {{ $bgColor }} flex items-center justify-center ring-8 ring-white">
                                                    @if($activity->action == 'created')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                    @elseif($activity->action == 'updated')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                    @elseif($activity->action == 'deleted')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        {{ $activity->description ?? ucfirst($activity->action) . ' ' . $activity->module }}
                                                    </p>
                                                    @if($activity->user)
                                                        <p class="text-xs text-gray-500">oleh {{ $activity->user->name }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right text-xs whitespace-nowrap text-gray-500">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>Belum ada aktivitas tercatat</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const chartData = @json($monthlyRevenue);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(d => d.month),
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: chartData.map(d => d.revenue),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderRadius: 6,
                        },
                        {
                            label: 'Pengeluaran',
                            data: chartData.map(d => d.expenses),
                            backgroundColor: 'rgba(239, 68, 68, 0.6)',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
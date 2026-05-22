@extends('layouts.superadmin')

@section('title', 'Super Admin Dashboard')
@section('header', 'Super Admin Dashboard')

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Tenants -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Tenant</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_tenants'] }}</p>
                    <div class="flex items-center mt-2">
                        @if($stats['tenant_growth'] >= 0)
                            <span class="text-green-500 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $stats['tenant_growth'] }}%
                            </span>
                        @else
                            <span class="text-red-500 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ abs($stats['tenant_growth']) }}%
                            </span>
                        @endif
                        <span class="text-gray-400 text-sm ml-2">growth</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Tenants -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tenant Aktif</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['active_tenants'] }}</p>
                    <p class="text-sm text-gray-400 mt-2">{{ $stats['suspended_tenants'] }} suspended</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total User</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_users'] }}</p>
                    <p class="text-sm text-green-500 mt-2">+{{ $stats['new_users_this_month'] }} bulan ini</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        Rp {{ number_format($stats['total_revenue'] / 1000000, 1) }}M
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
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Tenant Growth Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Pertumbuhan Tenant</h3>
                <span class="text-sm text-gray-500">12 bulan terakhir</span>
            </div>
            <canvas id="tenantGrowthChart" height="100"></canvas>
        </div>

        <!-- Subscription Distribution -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Distribusi Paket</h3>
            <canvas id="subscriptionChart" height="200"></canvas>
            <div class="mt-4 space-y-2">
                @foreach($subscriptionDistribution as $plan)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $plan['color'] }}"></div>
                            <span class="text-gray-600">{{ $plan['name'] }}</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $plan['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Revenue Bulanan</h3>
            <span class="text-sm text-gray-500">Pendapatan dari subscription</span>
        </div>
        <canvas id="revenueChart" height="80"></canvas>
    </div>

    <!-- Recent Tenants & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Tenants -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Tenant Terbaru</h3>
                    <a href="{{ route('superadmin.tenants.index') }}"
                        class="text-sm text-primary-600 hover:text-primary-700">Lihat Semua</a>
                </div>
            </div>
            <div class="divide-y">
                @forelse($recentTenants as $tenant)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold mr-3">
                                {{ strtoupper(substr($tenant->company_name ?? $tenant->owner->name ?? 'T', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $tenant->company_name ?? 'Unnamed' }}</p>
                                <p class="text-xs text-gray-500">{{ $tenant->owner->email ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                @if($tenant->status == 'active') bg-green-100 text-green-800
                                                                @elseif($tenant->status == 'suspended') bg-red-100 text-red-800
                                                                @else bg-gray-100 text-gray-800
                                                                @endif">
                                {{ ucfirst($tenant->status) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $tenant->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        Belum ada tenant terdaftar
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="{{ route('superadmin.tenants.index') }}"
                    class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Kelola Tenant</p>
                        <p class="text-xs text-gray-500">Lihat data tenant</p>
                    </div>
                </a>

                <a href="{{ route('superadmin.plans.index') }}"
                    class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Paket Langganan</p>
                        <p class="text-xs text-gray-500">Atur harga paket</p>
                    </div>
                </a>

                <a href="{{ route('superadmin.audit-logs') }}"
                    class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Audit Log</p>
                        <p class="text-xs text-gray-500">Aktivitas sistem</p>
                    </div>
                </a>

                {{-- Added Documentation Link --}}
                <div
                    class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors cursor-pointer">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Dokumentasi API</p>
                        <p class="text-xs text-gray-500">Integrasi sistem</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="mt-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Aktivitas Sistem Terbaru</h3>
                    <a href="{{ route('superadmin.audit-logs') }}"
                        class="text-sm text-primary-600 hover:text-primary-700">Lihat Semua</a>
                </div>
            </div>
            <div class="p-6">
                @if(isset($recentActivities) && $recentActivities->count() > 0)
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($recentActivities as $activity)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span
                                                    class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
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
            // Tenant Growth Chart
            const tenantCtx = document.getElementById('tenantGrowthChart').getContext('2d');
            const tenantData = @json($tenantGrowth);

            new Chart(tenantCtx, {
                type: 'line',
                data: {
                    labels: tenantData.map(d => d.month),
                    datasets: [{
                        label: 'Tenant Baru',
                        data: tenantData.map(d => d.count),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // Subscription Distribution Chart
            const subCtx = document.getElementById('subscriptionChart').getContext('2d');
            const subData = @json($subscriptionDistribution);

            new Chart(subCtx, {
                type: 'doughnut',
                data: {
                    labels: subData.map(d => d.name),
                    datasets: [{
                        data: subData.map(d => d.count),
                        backgroundColor: subData.map(d => d.color),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueData = @json($revenueData);

            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: revenueData.map(d => d.month),
                    datasets: [{
                        label: 'Revenue',
                        data: revenueData.map(d => d.revenue),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
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
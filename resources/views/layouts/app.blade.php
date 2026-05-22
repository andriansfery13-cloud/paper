<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { "50": "#eff6ff", "100": "#dbeafe", "200": "#bfdbfe", "300": "#93c5fd", "400": "#60a5fa", "500": "#3b82f6", "600": "#2563eb", "700": "#1d4ed8", "800": "#1e40af", "900": "#1e3a8a", "950": "#172554" }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Heroicons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@heroicons/vue@2.0.18/dist/heroicons.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }

        .sidebar-item.active {
            background-color: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            border-right: 3px solid #2563eb;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 min-h-screen" x-data="{ sidebarOpen: true, profileOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="bg-white shadow-lg transition-all duration-300 flex flex-col"
            :class="sidebarOpen ? 'w-64' : 'w-20'">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-center border-b px-4">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">P</span>
                    </div>
                    <span x-show="sidebarOpen" x-cloak class="font-bold text-xl text-gray-800">Paperly</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <div class="px-3 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'active' : 'text-gray-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                    </a>

                    <!-- Master Data Group -->
                    @if(auth()->user()->tenant->hasMenuAccess('clients') || auth()->user()->tenant->hasMenuAccess('products'))
                        @php
                            $masterActive = request()->routeIs('clients.*') || request()->routeIs('products.*');
                        @endphp
                        <div x-data="{ open: {{ $masterActive ? 'true' : 'false' }} }">
                            <button @click="open = !open" x-show="sidebarOpen"
                                class="w-full flex items-center justify-between px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-900 transition-colors">
                                Master Data
                                <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="!sidebarOpen" class="px-3 py-2 mt-2 border-t border-gray-100"></div>
                            <div x-show="open || !sidebarOpen" x-collapse class="space-y-1">
                                @if(auth()->user()->tenant->hasMenuAccess('clients'))
                                    <a href="{{ route('clients.index') }}"
                                        class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('clients.*') ? 'active' : 'text-gray-700' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        <span x-show="sidebarOpen" x-cloak>Clients</span>
                                    </a>
                                @endif

                                @if(auth()->user()->tenant->hasMenuAccess('products'))
                                    <a href="{{ route('products.index') }}"
                                        class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('products.*') ? 'active' : 'text-gray-700' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <span x-show="sidebarOpen" x-cloak>Products</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Transaction Group -->
                    @php
                        $transActive = request()->routeIs('invoices.*') || request()->routeIs('quotations.*') || request()->routeIs('payments.*') || request()->routeIs('receipts.*') || request()->routeIs('delivery-notes.*');
                    @endphp
                    <div x-data="{ open: {{ $transActive ? 'true' : 'false' }} }">
                        <button @click="open = !open" x-show="sidebarOpen"
                            class="w-full flex items-center justify-between px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-900 transition-colors">
                            Transaksi
                            <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="!sidebarOpen" class="px-3 py-2 mt-2 border-t border-gray-100"></div>
                        <div x-show="open || !sidebarOpen" x-collapse class="space-y-1">


                            @if(auth()->user()->tenant->hasMenuAccess('quotations'))
                                <a href="{{ route('quotations.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('quotations.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Quotation</span>
                                </a>
                            @endif

                            @if(auth()->user()->tenant->hasMenuAccess('invoices'))
                                <a href="{{ route('invoices.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('invoices.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Invoice</span>
                                </a>
                            @endif

                            @if(auth()->user()->tenant->hasMenuAccess('payments'))
                                <a href="{{ route('payments.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('payments.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Pembayaran</span>
                                </a>
                            @endif

                            @if(auth()->user()->tenant->hasMenuAccess('receipts'))
                                <a href="{{ route('receipts.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('receipts.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Kwitansi</span>
                                </a>
                            @endif

                            @if(auth()->user()->tenant->hasMenuAccess('delivery_notes'))
                                <a href="{{ route('delivery-notes.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('delivery-notes.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Surat Jalan</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Finance Group -->
                    @php
                        $financeActive = request()->routeIs('finance.expenses.*') || request()->routeIs('finance.expense-categories.*') || request()->routeIs('finance.income.*') || request()->routeIs('finance.payables.*') || request()->routeIs('finance.receivables.*') || request()->routeIs('finance.reports.*');
                    @endphp
                    <div x-data="{ open: {{ $financeActive ? 'true' : 'false' }} }">
                        <button @click="open = !open" x-show="sidebarOpen"
                            class="w-full flex items-center justify-between px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-900 transition-colors">
                            Keuangan
                            <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="!sidebarOpen" class="px-3 py-2 mt-2 border-t border-gray-100"></div>
                        <div x-show="open || !sidebarOpen" x-collapse class="space-y-1">
                            @if(auth()->user()->tenant->hasMenuAccess('expenses'))
                                <a href="{{ route('finance.expenses.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('finance.expenses.*') || request()->routeIs('finance.expense-categories.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Pengeluaran</span>
                                </a>
                            @endif

                            @if(auth()->user()->tenant->hasMenuAccess('income'))
                                <a href="{{ route('finance.income.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('finance.income.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Pemasukan</span>
                                </a>
                            @endif

                            @if(auth()->user()->tenant->hasMenuAccess('payments'))
                                <a href="{{ route('finance.payables.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('finance.payables.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Hutang</span>
                                </a>
                            @endif

                            @if(auth()->user()->tenant->hasMenuAccess('payments'))
                                <a href="{{ route('finance.receivables.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('finance.receivables.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Piutang</span>
                                </a>
                            @endif

                            @if(auth()->user()->tenant->hasMenuAccess('reports'))
                                <a href="{{ route('finance.reports.profit-loss') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('finance.reports.*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Laporan Keuangan</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Settings Group -->
                    @php
                        $settingsActive = request()->routeIs('settings.company') || request()->routeIs('settings.invoice') || request()->routeIs('settings.email') || request()->routeIs('settings.templates*') || request()->routeIs('settings.subscription*') || request()->routeIs('settings.users*');
                    @endphp
                    <div x-data="{ open: {{ $settingsActive ? 'true' : 'false' }} }">
                        <button @click="open = !open" x-show="sidebarOpen"
                            class="w-full flex items-center justify-between px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-900 transition-colors">
                            Pengaturan
                            <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="!sidebarOpen" class="px-3 py-2 mt-2 border-t border-gray-100"></div>
                        <div x-show="open || !sidebarOpen" x-collapse class="space-y-1">
                            <a href="{{ route('settings.company') }}"
                                class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('settings.company') ? 'active' : 'text-gray-700' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak>Perusahaan</span>
                            </a>

                            <a href="{{ route('settings.invoice') }}"
                                class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('settings.invoice') ? 'active' : 'text-gray-700' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak>Invoice</span>
                            </a>

                            <a href="{{ route('settings.email') }}"
                                class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('settings.email') ? 'active' : 'text-gray-700' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak>Email</span>
                            </a>

                            @if(auth()->user()->tenant->hasMenuAccess('templates'))
                                <a href="{{ route('settings.templates.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('settings.templates*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Template Dokumen</span>
                                </a>
                            @endif

                            <a href="{{ route('settings.subscription') }}"
                                class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('settings.subscription*') ? 'active' : 'text-gray-700' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak>Langganan & Token</span>
                            </a>

                            @if(auth()->user()->tenant->hasMenuAccess('users'))
                                <a href="{{ route('settings.users.index') }}"
                                    class="sidebar-item flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('settings.users*') ? 'active' : 'text-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak>Kelola User</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- User Info -->
            <div class="border-t p-4" x-show="sidebarOpen" x-cloak>
                <div class="flex items-center">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full" alt="">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->tenant->company_name ?? 'Admin' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="ml-4 text-lg font-semibold text-gray-800">@yield('header', 'Dashboard')</h1>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="p-2 rounded-lg hover:bg-gray-100 relative">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full" alt="">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-cloak
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-1 z-50">
                            <a href="{{ route('settings.company') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Pengaturan
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center text-green-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center text-red-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div
                        class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-center text-yellow-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ session('warning') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Quota Limit Popup -->
    @if(auth()->check() && auth()->user()->tenant)
        @include('components.quota-popup')

        @if(session('quota_exceeded'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    showQuotaExceeded(
                        '{{ session('quota_type', 'all') }}',
                        '{{ session('quota_message', 'Kuota telah habis.') }}',
                        {!! session('quota_usage', '{}') !!}
                    );
                });
            </script>
        @endif
    @endif

    @stack('scripts')
</body>

</html>
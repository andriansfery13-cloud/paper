<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc',
                            400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1',
                            800: '#075985', 900: '#0c4a6e',
                        }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .sidebar-item.active {
            background-color: #0ea5e9;
            color: white;
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans bg-gray-50 min-h-screen" x-data="{ sidebarOpen: true }">
    <div class="flex">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="fixed inset-y-0 left-0 z-30 bg-white border-r border-gray-200 transition-all duration-300 ease-in-out">

            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">S</span>
                    </div>
                    <span x-show="sidebarOpen" x-cloak class="ml-3 font-semibold text-gray-800">Super Admin</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('superadmin.dashboard') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.dashboard') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                </a>

                <!-- Tenant Management -->
                <a href="{{ route('superadmin.tenants.index') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.tenants.*') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Manajemen Tenant</span>
                </a>

                <!-- Subscription Plans -->
                <a href="{{ route('superadmin.plans.index') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.plans.*') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Paket Langganan</span>
                </a>

                <!-- Subscription Transactions -->
                <a href="{{ route('superadmin.subscriptions.index') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Transaksi Langganan</span>
                </a>


                <!-- Audit Logs -->
                <a href="{{ route('superadmin.audit-logs') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.audit-logs*') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Audit Log</span>
                </a>

                <!-- System Templates -->
                <a href="{{ route('superadmin.templates.index') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.templates.*') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Template Sistem</span>
                </a>

                <!-- Settings -->
                <a href="{{ route('superadmin.settings') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.settings*') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Pengaturan</span>
                </a>

                <!-- Notification Settings -->
                <a href="{{ route('superadmin.settings.notifications') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.settings.notifications') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Notifikasi (Gateway)</span>
                </a>

                <!-- SMTP Settings -->
                <a href="{{ route('superadmin.settings.smtp') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.settings.smtp') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Email Server (SMTP)</span>
                </a>

                <!-- API Docs -->
                <a href="{{ route('superadmin.api-docs') }}"
                    class="sidebar-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 {{ request()->routeIs('superadmin.api-docs') ? 'active' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>API Docs</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
                <div class="flex items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'SA') }}&background=0ea5e9&color=fff"
                        class="w-10 h-10 rounded-full" alt="">
                    <div x-show="sidebarOpen" x-cloak class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-800 truncate">
                            {{ auth()->user()->name ?? 'Super Admin' }}
                        </p>
                        <p class="text-xs text-gray-500">Super Admin</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" x-show="sidebarOpen" x-cloak>
                        @csrf
                        <button type="submit" class="p-1 text-gray-400 hover:text-red-500" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="flex-1 min-h-screen transition-all duration-300">
            <!-- Header -->
            <header class="sticky top-0 z-20 bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">@yield('header', 'Dashboard')</h1>
                    <div class="flex items-center gap-4">
                        @if(session('success'))
                            <div class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="px-4 py-2 bg-red-100 text-red-800 rounded-lg text-sm">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
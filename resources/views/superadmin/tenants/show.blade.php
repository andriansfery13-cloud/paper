@extends('layouts.superadmin')

@section('title', 'Detail Tenant')
@section('header', 'Detail Tenant')

@section('content')
    <div class="mb-6">
        <a href="{{ route('superadmin.tenants.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Tenant
        </a>
    </div>

    <!-- Tenant Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white text-2xl font-bold mr-4">
                    {{ strtoupper(substr($tenant->company_name ?? 'T', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $tenant->company_name ?? 'Unnamed Tenant' }}</h2>
                    <p class="text-gray-500">{{ $tenant->owner->email ?? '-' }}</p>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($tenant->status == 'active') bg-green-100 text-green-800
                            @elseif($tenant->status == 'suspended') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($tenant->status) }}
                        </span>
                        @if($tenant->currentPlan)
                            <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $tenant->currentPlan->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('superadmin.tenants.impersonate', $tenant) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2"
                        onclick="return confirm('Login sebagai admin tenant ini? Anda akan logout dari Super Admin.');"
                        title="Login sebagai Admin Tenant (Bypass OTP)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Login as Admin
                    </button>
                </form>
                <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Edit
                </a>
                @if($tenant->status == 'active')
                    <form action="{{ route('superadmin.tenants.suspend', $tenant) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                            onclick="return confirm('Yakin ingin suspend tenant ini?')">
                            Suspend
                        </button>
                    </form>
                @else
                    <form action="{{ route('superadmin.tenants.activate', $tenant) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Activate
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Users</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Invoices</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_invoices'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Clients</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_clients'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
            <p class="text-3xl font-bold text-green-600 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Company Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Perusahaan</h3>
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Nama Perusahaan</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->company_name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->email ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->phone ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">NPWP</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->npwp ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                    <dd class="text-sm text-gray-900 text-right max-w-xs">{{ $tenant->address ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Website</dt>
                    <dd class="text-sm text-blue-600"><a href="{{ $tenant->website }}" target="_blank">{{ $tenant->website ?? '-' }}</a></dd>
                </div>
            </dl>
        </div>

        <!-- Subscription Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Langganan</h3>
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Paket Saat Ini</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->currentPlan->name ?? 'Free' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($tenant->status == 'active') bg-green-100 text-green-800
                            @elseif($tenant->status == 'suspended') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Berakhir Pada</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d M Y') : 'Tidak terbatas' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Token Tersisa</dt>
                    <dd class="text-sm text-gray-900">{{ number_format($tenant->token_balance ?? 0) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Terdaftar</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->created_at->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Users ({{ $tenant->users->count() }})</h3>
            <div class="divide-y max-h-64 overflow-y-auto">
                @forelse($tenant->users as $user)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 bg-gray-100 rounded text-gray-600">
                            {{ $user->roles->pluck('name')->first() ?? 'User' }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Tidak ada user</p>
                @endforelse
            </div>
        </div>

        <!-- Subscription History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Langganan</h3>
            <div class="divide-y max-h-64 overflow-y-auto">
                @forelse($tenant->subscriptionHistories->sortByDesc('created_at') as $history)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $history->plan->name ?? 'Unknown Plan' }}</p>
                            <p class="text-xs text-gray-500">{{ $history->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-sm font-semibold text-green-600">Rp {{ number_format($history->amount_paid, 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-500 capitalize">{{ $history->status }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Belum ada riwayat langganan</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

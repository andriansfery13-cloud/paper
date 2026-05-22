@extends('layouts.superadmin')

@section('title', 'Paket Langganan')
@section('header', 'Paket Langganan')

@section('content')
    <!-- Header with Create Button -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-500">Kelola paket langganan dan hak akses menu untuk tenant</p>
        </div>
        <a href="{{ route('superadmin.plans.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Paket
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($plans as $plan)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden
                {{ $plan->slug === 'professional' ? 'ring-2 ring-primary-500' : '' }}">

                @if($plan->slug === 'professional')
                    <div class="bg-primary-600 text-white text-center py-1 text-sm font-medium">
                        Paling Populer
                    </div>
                @endif

                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                    <p class="text-gray-500 text-sm mt-1">{{ $plan->description ?? 'Paket ' . $plan->name }}</p>

                    <div class="mt-4">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-bold text-gray-900">
                                {{ $plan->price_monthly > 0 ? 'Rp ' . number_format($plan->price_monthly / 1000, 0) . 'K' : 'Gratis' }}
                            </span>
                            @if($plan->price_monthly > 0)
                                <span class="text-gray-500 ml-1">/bulan</span>
                            @endif
                        </div>
                        @if($plan->price_yearly > 0)
                            <p class="text-sm text-gray-400 mt-1">
                                Rp {{ number_format($plan->price_yearly / 1000, 0) }}K/tahun (hemat {{ round((1 - $plan->price_yearly / ($plan->price_monthly * 12)) * 100) }}%)
                            </p>
                        @endif
                    </div>

                    <ul class="mt-6 space-y-3">
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $plan->max_invoices == -1 ? 'Unlimited' : $plan->max_invoices }} Invoice
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $plan->max_clients == -1 ? 'Unlimited' : $plan->max_clients }} Client
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }} User
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $plan->max_products == -1 ? 'Unlimited' : $plan->max_products }} Produk
                        </li>

                        @if($plan->has_payment_gateway)
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Payment Gateway
                            </li>
                        @else
                            <li class="flex items-center text-sm text-gray-400">
                                <svg class="w-5 h-5 text-gray-300 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="line-through">Payment Gateway</span>
                            </li>
                        @endif

                        @if($plan->has_api_access)
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                API Access
                            </li>
                        @else
                            <li class="flex items-center text-sm text-gray-400">
                                <svg class="w-5 h-5 text-gray-300 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="line-through">API Access</span>
                            </li>
                        @endif

                        @if($plan->has_custom_template)
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Custom Template
                            </li>
                        @else
                            <li class="flex items-center text-sm text-gray-400">
                                <svg class="w-5 h-5 text-gray-300 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="line-through">Custom Template</span>
                            </li>
                        @endif
                    </ul>

                    <div class="mt-6 pt-4 border-t">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Tenant Aktif</span>
                            <span class="font-semibold text-gray-900">{{ $plan->tenants->where('status', 'active')->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="text-gray-500">Status</span>
                            @if($plan->is_active)
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Nonaktif</span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('superadmin.plans.edit', $plan) }}" 
                            class="flex-1 text-center px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Edit
                        </a>
                        <form action="{{ route('superadmin.plans.destroy', $plan) }}" method="POST" class="flex-1"
                            onsubmit="return confirm('Yakin ingin menghapus paket {{ $plan->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                {{ $plan->tenants()->where('status', 'active')->count() > 0 ? 'disabled' : '' }}>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 text-center py-12 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p>Belum ada paket langganan</p>
            </div>
        @endforelse
    </div>
@endsection
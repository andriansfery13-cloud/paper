@extends('layouts.superadmin')

@section('title', 'Edit Paket Langganan')
@section('header', 'Edit Paket: ' . $plan->name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('superadmin.plans.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Paket
        </a>
    </div>

    <form action="{{ route('superadmin.plans.update', $plan) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $plan->sort_order) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('description', $plan->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Harga</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Bulanan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price_monthly" value="{{ old('price_monthly', $plan->price_monthly) }}" min="0" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Tahunan (Rp)</label>
                    <input type="number" name="price_yearly" value="{{ old('price_yearly', $plan->price_yearly) }}" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Masa Trial (Hari)</label>
                    <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Token Termasuk</label>
                    <input type="number" name="included_tokens" value="{{ old('included_tokens', $plan->included_tokens) }}" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <!-- Limits -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Batasan Kuota</h3>
            <p class="text-sm text-gray-500 mb-4">Masukkan -1 untuk unlimited</p>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Invoice</label>
                    <input type="number" name="max_invoices" value="{{ old('max_invoices', $plan->max_invoices) }}" min="-1"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Client</label>
                    <input type="number" name="max_clients" value="{{ old('max_clients', $plan->max_clients) }}" min="-1"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max User</label>
                    <input type="number" name="max_users" value="{{ old('max_users', $plan->max_users) }}" min="-1"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Produk</label>
                    <input type="number" name="max_products" value="{{ old('max_products', $plan->max_products) }}" min="-1"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Penawaran</label>
                    <input type="number" name="max_quotations" value="{{ old('max_quotations', $plan->max_quotations) }}" min="-1"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Fitur Tambahan</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <label class="flex items-center">
                    <input type="checkbox" name="has_wa_gateway" value="1" {{ old('has_wa_gateway', $plan->has_wa_gateway) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">WhatsApp Gateway</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="has_payment_gateway" value="1" {{ old('has_payment_gateway', $plan->has_payment_gateway) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">Payment Gateway</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="has_api_access" value="1" {{ old('has_api_access', $plan->has_api_access) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">API Access</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="has_custom_template" value="1" {{ old('has_custom_template', $plan->has_custom_template) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">Custom Template</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="has_recurring_invoice" value="1" {{ old('has_recurring_invoice', $plan->has_recurring_invoice) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">Invoice Recurring</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="has_multi_currency" value="1" {{ old('has_multi_currency', $plan->has_multi_currency) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">Multi Currency</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">Paket Aktif</span>
                </label>
            </div>
        </div>

        <!-- Menu Permissions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Hak Akses Menu</h3>
            <p class="text-sm text-gray-500 mb-4">Pilih menu yang dapat diakses oleh tenant dengan paket ini</p>
            @php $currentPerms = old('menu_permissions', $plan->menu_permissions ?? []); @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($menus as $key => $menu)
                    <label class="flex items-center p-3 border rounded-lg {{ $menu['required'] ? 'bg-gray-50' : 'hover:bg-gray-50' }}">
                        <input type="checkbox" name="menu_permissions[]" value="{{ $key }}"
                            {{ $menu['required'] ? 'checked disabled' : '' }}
                            {{ in_array($key, $currentPerms) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        @if($menu['required'])
                            <input type="hidden" name="menu_permissions[]" value="{{ $key }}">
                        @endif
                        <span class="ml-2 text-sm text-gray-700">
                            {{ $menu['label'] }}
                            @if($menu['required'])
                                <span class="text-xs text-gray-400">(wajib)</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Tenant Count Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-blue-800">
                    Paket ini memiliki <strong>{{ $plan->tenants()->where('status', 'active')->count() }}</strong> tenant aktif.
                    Perubahan akan mempengaruhi semua tenant yang menggunakan paket ini.
                </span>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('superadmin.plans.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Update Paket
            </button>
        </div>
    </form>
@endsection

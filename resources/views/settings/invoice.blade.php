@extends('layouts.app')

@section('title', 'Pengaturan Invoice')
@section('header', 'Pengaturan Invoice & Dokumen')

@section('content')
    <div class="max-w-4xl">
        <!-- Settings Navigation -->
        <div class="mb-6 border-b">
            <nav class="flex gap-4">
                <a href="{{ route('settings.company') }}"
                    class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('settings.company') ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Perusahaan
                </a>
                <a href="{{ route('settings.invoice') }}"
                    class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('settings.invoice') ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Invoice & Dokumen
                </a>
                <a href="{{ route('settings.email') }}"
                    class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('settings.email') ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Email
                </a>
            </nav>
        </div>

        <form action="{{ route('settings.invoice.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Document Prefixes -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Prefix Dokumen</h3>
                <p class="text-sm text-gray-500 mb-4">Atur prefix untuk nomor dokumen yang akan dibuat secara otomatis.</p>

                <div class="grid grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice</label>
                        <input type="text" name="invoice_prefix"
                            value="{{ old('invoice_prefix', $tenant->invoice_prefix ?? 'INV') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="INV">
                        <p class="text-xs text-gray-500 mt-1">Contoh: INV/2026/01/00001</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quotation</label>
                        <input type="text" name="quotation_prefix"
                            value="{{ old('quotation_prefix', $tenant->quotation_prefix ?? 'QUO') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="QUO">
                        <p class="text-xs text-gray-500 mt-1">Contoh: QUO/2026/01/00001</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Receipt</label>
                        <input type="text" name="receipt_prefix"
                            value="{{ old('receipt_prefix', $tenant->receipt_prefix ?? 'RCP') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="RCP">
                        <p class="text-xs text-gray-500 mt-1">Contoh: RCP/2026/01/00001</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Surat Jalan</label>
                        <input type="text" name="delivery_prefix"
                            value="{{ old('delivery_prefix', $tenant->delivery_prefix ?? 'DO') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="DO">
                        <p class="text-xs text-gray-500 mt-1">Contoh: DO/2026/01/00001</p>
                    </div>
                </div>
            </div>

            <!-- Default Settings -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Default</h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tarif Pajak Default (%)</label>
                        <input type="number" name="default_tax_rate"
                            value="{{ old('default_tax_rate', $tenant->invoice_settings['default_tax_rate'] ?? 11) }}"
                            min="0" max="100" step="0.1"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <p class="text-xs text-gray-500 mt-1">PPN untuk item baru (default 11%)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo Default (hari)</label>
                        <input type="number" name="default_payment_terms"
                            value="{{ old('default_payment_terms', $tenant->invoice_settings['default_payment_terms'] ?? 30) }}"
                            min="0" max="365"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <p class="text-xs text-gray-500 mt-1">Jumlah hari dari tanggal invoice</p>
                    </div>
                </div>
            </div>

            <!-- Default Notes & Terms -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Template Catatan & Syarat</h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Default</label>
                        <textarea name="default_notes" rows="4"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Catatan yang akan muncul di setiap invoice/quotation">{{ old('default_notes', $tenant->invoice_settings['default_notes'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Syarat & Ketentuan Default</label>
                        <textarea name="default_terms" rows="4"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Syarat pembayaran default">{{ old('default_terms', $tenant->invoice_settings['default_terms'] ?? 'Pembayaran dilakukan melalui transfer bank ke rekening yang tertera.') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection
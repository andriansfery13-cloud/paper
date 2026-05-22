@extends('layouts.app')

@section('title', 'Pengaturan Email')
@section('header', 'Pengaturan Email')

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

    @php
        $emailSettings = $tenant->settings['email'] ?? [];
    @endphp

    <form action="{{ route('settings.email.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Email Notifications Toggle -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Notifikasi Email</h3>
                    <p class="text-sm text-gray-500">Aktifkan untuk mengirim email otomatis ke client</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="email_notifications_enabled" value="0">
                    <input type="checkbox" name="email_notifications_enabled" value="1" 
                        {{ old('email_notifications_enabled', $emailSettings['notifications_enabled'] ?? false) ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                </label>
            </div>
        </div>

        <!-- Sender Settings -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Pengirim</h3>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengirim</label>
                    <input type="text" name="email_from_name" 
                        value="{{ old('email_from_name', $emailSettings['from_name'] ?? $tenant->company_name) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Nama yang muncul di email">
                    <p class="text-xs text-gray-500 mt-1">Nama pengirim yang akan ditampilkan</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email CC</label>
                    <input type="email" name="email_cc" 
                        value="{{ old('email_cc', $emailSettings['cc'] ?? '') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="admin@company.com">
                    <p class="text-xs text-gray-500 mt-1">Email akan di-CC ke alamat ini</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email BCC</label>
                    <input type="email" name="email_bcc" 
                        value="{{ old('email_bcc', $emailSettings['bcc'] ?? '') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="archive@company.com">
                    <p class="text-xs text-gray-500 mt-1">Email akan di-BCC ke alamat ini (tidak terlihat penerima)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Footer Email</label>
                    <input type="text" name="email_footer" 
                        value="{{ old('email_footer', $emailSettings['footer'] ?? '') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Terima kasih atas kepercayaan Anda.">
                </div>
            </div>
        </div>

        <!-- Email Templates -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Template Subject Email</h3>
            <p class="text-sm text-gray-500 mb-4">Gunakan placeholder: <code class="bg-gray-100 px-1 rounded">{company_name}</code>, <code class="bg-gray-100 px-1 rounded">{doc_number}</code>, <code class="bg-gray-100 px-1 rounded">{client_name}</code></p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Quotation</label>
                    <input type="text" name="quotation_email_subject" 
                        value="{{ old('quotation_email_subject', $emailSettings['quotation_subject'] ?? 'Penawaran dari {company_name} - {doc_number}') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Invoice</label>
                    <input type="text" name="invoice_email_subject" 
                        value="{{ old('invoice_email_subject', $emailSettings['invoice_subject'] ?? 'Invoice dari {company_name} - {doc_number}') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Konfirmasi Pembayaran</label>
                    <input type="text" name="payment_email_subject" 
                        value="{{ old('payment_email_subject', $emailSettings['payment_subject'] ?? 'Konfirmasi Pembayaran - {doc_number}') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <!-- SMTP Info -->
        <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
            <div class="flex">
                <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-800">Konfigurasi SMTP</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Pengaturan SMTP server dikonfigurasi melalui file <code class="bg-blue-100 px-1 rounded">.env</code>. 
                        Hubungi administrator jika perlu mengubah setting email server.
                    </p>
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

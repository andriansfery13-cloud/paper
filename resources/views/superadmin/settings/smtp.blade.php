@extends('layouts.superadmin')

@section('title', 'Analisa SMTP System')
@section('header', 'Konfigurasi SMTP')

@section('content')
    <div class="max-w-4xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Pengaturan Email Server (SMTP)</h2>
            <p class="text-gray-500 mb-6 text-sm">Konfigurasi ini akan digunakan untuk mengirim email verifikasi, notifikasi
                sistem, dan OTP via Email.</p>

            <form action="{{ route('superadmin.settings.smtp.update') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}" required
                            placeholder="smtp.gmail.com"
                            class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                        <input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}" required
                            placeholder="587"
                            class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                        <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                        <input type="password" name="mail_password" placeholder="Biarkan kosong jika tidak diubah"
                            class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                        <select name="mail_encryption"
                            class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS
                            </option>
                            <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL
                            </option>
                            <option value="" {{ empty($settings['mail_encryption']) ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <div class="col-span-2 border-t pt-6 mt-2">
                        <h3 class="text-md font-medium text-gray-700 mb-4">Identitas Pengirim</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Address</label>
                                <input type="email" name="mail_from_address"
                                    value="{{ $settings['mail_from_address'] ?? '' }}" required
                                    placeholder="noreply@example.com"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                                <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}"
                                    required placeholder="Nama Aplikasi"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
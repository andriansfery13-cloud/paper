@extends('layouts.superadmin')

@section('title', 'Pengaturan Sistem')
@section('header', 'Pengaturan Sistem')

@section('content')
    <div class="max-w-4xl">
        <!-- Midtrans Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Pengaturan Midtrans</h2>
                    <p class="text-sm text-gray-500">Konfigurasi API Key untuk payment gateway</p>
                </div>
            </div>

            <form action="{{ route('superadmin.settings.midtrans') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Server Key</label>
                    <input type="password" name="midtrans_server_key" value="{{ $settings['midtrans_server_key'] }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="SB-Mid-server-xxxxxxxx">
                    <p class="text-xs text-gray-500 mt-1">Server key dari dashboard Midtrans</p>
                    @error('midtrans_server_key')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Key</label>
                    <input type="text" name="midtrans_client_key" value="{{ $settings['midtrans_client_key'] }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="SB-Mid-client-xxxxxxxx">
                    <p class="text-xs text-gray-500 mt-1">Client key untuk Snap.js</p>
                    @error('midtrans_client_key')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                    <select name="midtrans_is_production"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="false" {{ $settings['midtrans_is_production'] == 'false' ? 'selected' : '' }}>Sandbox
                            (Testing)</option>
                        <option value="true" {{ $settings['midtrans_is_production'] == 'true' ? 'selected' : '' }}>Production
                            (Live)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Gunakan Sandbox untuk testing, Production untuk transaksi nyata
                    </p>
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Simpan Pengaturan Midtrans
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <h3 class="font-semibold text-blue-800 mb-2">📘 Cara Mendapatkan API Key Midtrans</h3>
            <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                <li>Login ke <a href="https://dashboard.sandbox.midtrans.com" target="_blank" class="underline">Dashboard
                        Midtrans</a></li>
                <li>Pilih menu Settings → Access Keys</li>
                <li>Copy Server Key dan Client Key</li>
                <li>Pastikan mengaktifkan Snap di menu Settings → Snap Preferences</li>
            </ol>
        </div>
    </div>
@endsection
@extends('layouts.auth')

@section('title', 'Registrasi Berhasil')

@section('content')
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
            <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                </path>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-2">Cek Email Anda</h2>
        <p class="text-gray-600 mb-6">
            Kami telah mengirimkan link verifikasi ke alamat email perusahaan yang Anda daftarkan.
            Silakan klik link tersebut untuk mengaktifkan akun Anda dan menikmati paket Free kami.
        </p>

        <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-500 mb-6">
            <p>Belum menerima email?</p>
            <p class="mt-1">Cek folder spam atau pastikan email yang Anda masukkan benar.</p>
        </div>

        <a href="{{ route('login') }}" class="text-primary-600 font-medium hover:text-primary-700">
            Kembali ke Halaman Login
        </a>
    </div>
@endsection
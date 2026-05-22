@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Masuk ke Akun</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-5">
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                    placeholder="nama@perusahaan.com">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    placeholder="••••••••">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember"
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                </label>
                <a href="#" class="text-sm text-primary-600 hover:text-primary-700">Lupa password?</a>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all shadow-lg shadow-primary-500/30">
                Masuk
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary-600 font-medium hover:text-primary-700">Daftar
                sekarang</a>
        </p>
    </div>

    <!-- Demo Accounts -->
    <div class="mt-6 p-4 bg-gray-50 rounded-xl">
        <p class="text-xs text-gray-500 text-center mb-2">Demo Account</p>
        <div class="text-xs text-gray-600 space-y-1">
            <p><strong>Super Admin:</strong> superadmin@paper.test / password</p>
            <p><strong>Tenant:</strong> admin@demo-company.com / password</p>
        </div>
    </div>
@endsection
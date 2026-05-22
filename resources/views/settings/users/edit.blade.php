@extends('layouts.app')

@section('title', 'Edit User')
@section('header', 'Edit User: ' . $user->name)

@section('content')
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('settings.users.index') }}"
                class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali ke Daftar User
            </a>
        </div>

        <form action="{{ route('settings.users.update', $user) }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Avatar Preview -->
                <div class="flex items-center">
                    <img src="{{ $user->avatar_url }}" class="w-16 h-16 rounded-full" alt="">
                    <div class="ml-4">
                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">
                            @if($user->is_owner)
                                <span class="text-purple-600">Owner</span>
                            @else
                                Staff
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        No. Telepon
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Password -->
                <div class="border-t pt-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Ubah Password (Opsional)</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Password Baru
                            </label>
                            <input type="password" name="password"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Kosongkan jika tidak ingin mengubah">
                            @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Konfirmasi Password Baru
                            </label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>

                <!-- Active Status -->
                @if(!$user->is_owner)
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-700">User aktif (dapat login)</span>
                        </label>
                    </div>
                @endif
            </div>

            <!-- Submit -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('settings.users.index') }}"
                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Update User
                </button>
            </div>
        </form>
    </div>
@endsection
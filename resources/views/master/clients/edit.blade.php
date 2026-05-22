@extends('layouts.app')

@section('title', 'Edit Client')
@section('header', 'Edit Client')

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('clients.update', $client) }}" method="POST"
            class="bg-white rounded-xl shadow-sm border p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-6">
                <!-- Kode Client -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Client</label>
                    <input type="text" value="{{ $client->client_code }}" disabled
                        class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600">
                </div>

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Client <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $client->email) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Telepon -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Alamat -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="address" rows="2"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('address', $client->address) }}</textarea>
                </div>

                <!-- Kota -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                    <input type="text" name="city" value="{{ old('city', $client->city) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Provinsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                    <input type="text" name="province" value="{{ old('province', $client->province) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- NPWP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                    <input type="text" name="npwp" value="{{ old('npwp', $client->npwp) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Term Pembayaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term Pembayaran (Hari)</label>
                    <input type="number" name="payment_term_days"
                        value="{{ old('payment_term_days', $client->payment_term_days) }}" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Credit Limit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Credit Limit</label>
                    <input type="number" name="credit_limit" value="{{ old('credit_limit', $client->credit_limit) }}"
                        min="0" class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="1" {{ $client->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$client->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Catatan -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $client->notes) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('clients.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Update</button>
            </div>
        </form>
    </div>
@endsection
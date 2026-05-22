@extends('layouts.app')

@section('title', 'Detail Client')
@section('header', 'Detail Client')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Info -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $client->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $client->client_code }}</p>
                    </div>
                    <span
                        class="inline-flex px-3 py-1 text-sm font-medium rounded-full {{ $client->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $client->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $client->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Telepon</p>
                        <p class="font-medium">{{ $client->phone ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Alamat</p>
                        <p class="font-medium">{{ $client->address ?? '-' }}</p>
                        <p class="text-sm text-gray-600">{{ $client->city }}, {{ $client->province }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">NPWP</p>
                        <p class="font-medium">{{ $client->npwp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Term Pembayaran</p>
                        <p class="font-medium">{{ $client->payment_term_days }} hari</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Credit Limit</p>
                        <p class="font-medium">Rp {{ number_format($client->credit_limit, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6 pt-6 border-t">
                    <a href="{{ route('clients.edit', $client) }}"
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Edit</a>
                    <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}"
                        class="px-4 py-2 border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50">Buat
                        Invoice</a>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Invoice</span>
                        <span class="font-medium">{{ $client->invoices_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Piutang</span>
                        <span class="font-medium text-red-600">Rp
                            {{ number_format($client->outstanding_receivables, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Piutang Klien')
@section('header', 'Piutang Klien')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Piutang Klien</h2>
                <p class="text-gray-600">Daftar piutang dari invoice yang belum lunas</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                <p class="text-blue-100">Total Piutang</p>
                <p class="text-3xl font-bold">Rp {{ number_format($totalReceivables, 0, ',', '.') }}</p>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-6 text-white">
                <p class="text-red-100">Piutang Jatuh Tempo</p>
                <p class="text-3xl font-bold">Rp {{ number_format($overdueReceivables, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <form action="{{ route('finance.receivables.index') }}" method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama klien..."
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Cari</button>
                <a href="{{ route('finance.receivables.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-50">Reset</a>
            </form>
        </div>

        <!-- Clients Table -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klien
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Telepon</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Piutang</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($clients as $client)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-medium">
                                            {{ strtoupper(substr($client->name, 0, 2)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">{{ $client->name }}</p>
                                            @if($client->company)
                                                <p class="text-sm text-gray-500">{{ $client->company }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $client->email ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $client->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-blue-600">
                                    Rp {{ number_format($client->total_outstanding, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('finance.receivables.show', $client) }}"
                                        class="text-primary-600 hover:text-primary-900 font-medium">
                                        Lihat Detail →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="mt-2">Tidak ada piutang tercatat</p>
                                    <p class="text-sm text-gray-400">Semua invoice sudah lunas!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($clients->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $clients->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
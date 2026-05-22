@extends('layouts.app')

@section('title', 'Clients')
@section('header', 'Data Client')

@section('content')
    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari client..."
                    class="px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </div>
        <a href="{{ route('clients.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Client
        </a>
    </div>

    <!-- Client Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Piutang</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-primary-600">{{ $client->client_code }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $client->name }}</p>
                            <p class="text-xs text-gray-500">{{ $client->city }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->phone ?? '-' }}</td>
                        <td
                            class="px-6 py-4 text-sm text-right font-medium {{ $client->outstanding_receivables > 0 ? 'text-red-600' : 'text-gray-600' }}">
                            Rp {{ number_format($client->outstanding_receivables, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $client->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $client->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('clients.show', $client) }}" class="p-1 text-gray-500 hover:text-primary-600"
                                    title="Lihat">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}" class="p-1 text-gray-500 hover:text-primary-600"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Hapus client ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-500 hover:text-red-600" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                </path>
                            </svg>
                            <p>Belum ada client</p>
                            <a href="{{ route('clients.create') }}"
                                class="text-primary-600 hover:text-primary-700 mt-2 inline-block">Tambah Client Pertama</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($clients->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
@endsection
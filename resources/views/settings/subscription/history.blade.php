@extends('layouts.app')

@section('title', 'Riwayat Langganan')
@section('header', 'Riwayat Langganan')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="mb-4">
            <a href="{{ route('settings.subscription') }}"
                class="text-sm text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-900">Semua Riwayat Langganan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase font-medium">
                        <tr>
                            <th class="px-6 py-3 text-left">Paket</th>
                            <th class="px-6 py-3 text-left">Tanggal Transaksi</th>
                            <th class="px-6 py-3 text-right">Biaya</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($histories as $history)
                            <tr class="hover:bg-gray-50 text-sm">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $history->plan->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ optional($history->created_at)->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-900">Rp
                                    {{ number_format($history->amount_paid, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($history->status == 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @elseif($history->status == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Menunggu
                                        </span>
                                    @elseif($history->status == 'failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Gagal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($history->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada riwayat langganan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($histories->hasPages())
                <div class="p-4 border-t bg-gray-50">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Quotations')
@section('header', 'Quotation')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 border">
        <p class="text-sm text-gray-500">Total</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg p-4 border">
        <p class="text-sm text-gray-500">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-white rounded-lg p-4 border">
        <p class="text-sm text-gray-500">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
    </div>
    <div class="bg-white rounded-lg p-4 border">
        <p class="text-sm text-gray-500">Rejected</p>
        <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
    </div>
</div>

<!-- Filters & Actions -->
<div class="bg-white rounded-xl shadow-sm border mb-6">
    <div class="p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <!-- Search -->
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari quotation..."
                       class="px-4 py-2 border rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>

            <!-- Status Filter -->
            <form method="GET" id="filter-form">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <select name="status" onchange="this.form.submit()"
                        class="px-4 py-2 border rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Terkirim</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                </select>
            </form>
        </div>

        <a href="{{ route('quotations.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Quotation
        </a>
    </div>
</div>

<!-- Quotation Table -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Quotation</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berlaku Sampai</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($quotations as $quotation)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <a href="{{ route('quotations.show', $quotation) }}" class="text-primary-600 font-medium hover:text-primary-700">
                        {{ $quotation->quotation_number }}
                    </a>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm text-gray-900">{{ $quotation->client->name ?? '-' }}</p>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $quotation->quotation_date->format('d M Y') }}
                </td>
                <td class="px-6 py-4 text-sm {{ $quotation->valid_until < now() && !in_array($quotation->status, ['approved', 'converted']) ? 'text-red-600' : 'text-gray-600' }}">
                    {{ $quotation->valid_until->format('d M Y') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                    {{ $quotation->formatted_total }}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                        @switch($quotation->status)
                            @case('draft') bg-gray-100 text-gray-800 @break
                            @case('sent') bg-blue-100 text-blue-800 @break
                            @case('approved') bg-green-100 text-green-800 @break
                            @case('rejected') bg-red-100 text-red-800 @break
                            @case('expired') bg-yellow-100 text-yellow-800 @break
                            @case('converted') bg-purple-100 text-purple-800 @break
                        @endswitch">
                        {{ ucfirst($quotation->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('quotations.show', $quotation) }}" class="p-1 text-gray-500 hover:text-primary-600" title="Lihat">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('quotations.preview', $quotation) }}" target="_blank" class="p-1 text-gray-500 hover:text-primary-600" title="PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                        @if($quotation->canBeEdited())
                        <a href="{{ route('quotations.edit', $quotation) }}" class="p-1 text-gray-500 hover:text-primary-600" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>Belum ada quotation</p>
                    <a href="{{ route('quotations.create') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                        Buat Quotation Pertama
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($quotations->hasPages())
    <div class="px-6 py-4 border-t">
        {{ $quotations->links() }}
    </div>
    @endif
</div>
@endsection

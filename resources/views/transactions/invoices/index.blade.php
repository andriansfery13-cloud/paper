@extends('layouts.app')

@section('title', 'Invoices')
@section('header', 'Invoice')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 border">
        <p class="text-sm text-gray-500">Total</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg p-4 border">
        <p class="text-sm text-gray-500">Belum Lunas</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['unpaid'] }}</p>
    </div>
    <div class="bg-white rounded-lg p-4 border">
        <p class="text-sm text-gray-500">Jatuh Tempo</p>
        <p class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</p>
    </div>
    <div class="bg-white rounded-lg p-4 border">
        <p class="text-sm text-gray-500">Lunas</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['paid'] }}</p>
    </div>
</div>

<!-- Filters & Actions -->
<div class="bg-white rounded-xl shadow-sm border mb-6">
    <div class="p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <!-- Search -->
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari invoice..."
                       class="px-4 py-2 border rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>

            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()" form="filter-form"
                    class="px-4 py-2 border rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Terkirim</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Jatuh Tempo</option>
            </select>
        </div>

        <a href="{{ route('invoices.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Invoice
        </a>
    </div>
</div>

<!-- Invoice Table -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Invoice</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sisa</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($invoices as $invoice)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <a href="{{ route('invoices.show', $invoice) }}" class="text-primary-600 font-medium hover:text-primary-700">
                        {{ $invoice->invoice_number }}
                    </a>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm text-gray-900">{{ $invoice->client->name ?? '-' }}</p>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $invoice->invoice_date->format('d M Y') }}
                </td>
                <td class="px-6 py-4 text-sm {{ $invoice->due_date < now() && $invoice->status != 'paid' ? 'text-red-600' : 'text-gray-600' }}">
                    {{ $invoice->due_date->format('d M Y') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                    {{ $invoice->formatted_total }}
                </td>
                <td class="px-6 py-4 text-sm text-right {{ $invoice->amount_due > 0 ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    {{ $invoice->formatted_amount_due }}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                        @switch($invoice->status)
                            @case('draft') bg-gray-100 text-gray-800 @break
                            @case('sent') bg-blue-100 text-blue-800 @break
                            @case('viewed') bg-indigo-100 text-indigo-800 @break
                            @case('partial') bg-yellow-100 text-yellow-800 @break
                            @case('paid') bg-green-100 text-green-800 @break
                            @case('overdue') bg-red-100 text-red-800 @break
                            @case('cancelled') bg-gray-100 text-gray-800 @break
                        @endswitch">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('invoices.show', $invoice) }}" class="p-1 text-gray-500 hover:text-primary-600" title="Lihat">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('invoices.preview', $invoice) }}" target="_blank" class="p-1 text-gray-500 hover:text-primary-600" title="PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                        @if($invoice->canBeEdited())
                        <a href="{{ route('invoices.edit', $invoice) }}" class="p-1 text-gray-500 hover:text-primary-600" title="Edit">
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
                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>Belum ada invoice</p>
                    <a href="{{ route('invoices.create') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                        Buat Invoice Pertama
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($invoices->hasPages())
    <div class="px-6 py-4 border-t">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
@endsection

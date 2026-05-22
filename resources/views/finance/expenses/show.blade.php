@extends('layouts.app')

@section('title', 'Detail Pengeluaran')
@section('header', 'Detail Pengeluaran')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Detail Pengeluaran</h3>
                    <p class="text-sm text-gray-500">{{ $expense->transaction_date->format('d F Y') }}</p>
                </div>
                <span class="text-2xl font-bold text-red-600">{{ $expense->formatted_amount }}</span>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Kategori</label>
                        <p class="mt-1">
                            @if($expense->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium"
                                    style="background-color: {{ $expense->category->color ?? '#6b7280' }}20; color: {{ $expense->category->color ?? '#6b7280' }}">
                                    {{ $expense->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Metode Pembayaran</label>
                        <p class="mt-1 text-gray-900">{{ ucfirst(str_replace('_', ' ', $expense->payment_method ?? '-')) }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Deskripsi</label>
                    <p class="mt-1 text-gray-900">{{ $expense->description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Supplier</label>
                        <p class="mt-1 text-gray-900">{{ $expense->supplier->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">No. Referensi</label>
                        <p class="mt-1 text-gray-900">{{ $expense->reference_number ?? '-' }}</p>
                    </div>
                </div>

                @if($expense->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Catatan</label>
                        <p class="mt-1 text-gray-900">{{ $expense->notes }}</p>
                    </div>
                @endif

                @if($expense->receipt_image)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Bukti Pembayaran</label>
                        <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt"
                            class="max-w-sm rounded-lg border shadow-sm">
                    </div>
                @endif

                <div class="text-sm text-gray-500">
                    Dicatat oleh {{ $expense->creator->name ?? '-' }} pada {{ $expense->created_at->format('d M Y H:i') }}
                </div>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between">
                <a href="{{ route('finance.expenses.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors">
                    &larr; Kembali
                </a>
                <form action="{{ route('finance.expenses.destroy', $expense) }}" method="POST"
                    onsubmit="return confirm('Hapus pengeluaran ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Detail Pemasukan')
@section('header', 'Detail Pemasukan')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Detail Pemasukan</h3>
                    <p class="text-sm text-gray-500">{{ $income->transaction_date->format('d F Y') }}</p>
                </div>
                <span class="text-2xl font-bold text-green-600">{{ $income->formatted_amount }}</span>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Sumber</label>
                        <p class="mt-1">
                            @if($income->source == 'invoice_payment')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Pembayaran
                                    Invoice</span>
                            @elseif($income->source == 'manual')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">Input
                                    Manual</span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Lainnya</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Metode Pembayaran</label>
                        <p class="mt-1 text-gray-900">{{ ucfirst(str_replace('_', ' ', $income->payment_method ?? '-')) }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Deskripsi</label>
                    <p class="mt-1 text-gray-900">{{ $income->description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">No. Referensi</label>
                        <p class="mt-1 text-gray-900">{{ $income->reference_number ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Akun</label>
                        <p class="mt-1 text-gray-900">{{ $income->account_name ?? '-' }}</p>
                    </div>
                </div>

                @if($income->payment)
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <label class="block text-sm font-medium text-blue-800 mb-2">Terkait dengan Pembayaran Invoice</label>
                        <p class="text-blue-700">
                            Invoice: <strong>{{ $income->payment->invoice->invoice_number ?? '-' }}</strong>
                        </p>
                    </div>
                @endif

                @if($income->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Catatan</label>
                        <p class="mt-1 text-gray-900">{{ $income->notes }}</p>
                    </div>
                @endif

                <div class="text-sm text-gray-500">
                    Dicatat oleh {{ $income->creator->name ?? '-' }} pada {{ $income->created_at->format('d M Y H:i') }}
                </div>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between">
                <a href="{{ route('finance.income.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors">
                    &larr; Kembali
                </a>
                @if($income->source == 'manual')
                    <form action="{{ route('finance.income.destroy', $income) }}" method="POST"
                        onsubmit="return confirm('Hapus pemasukan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Detail Pembayaran')
@section('header', 'Detail Pembayaran')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6 pb-6 border-b">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $payment->payment_number }}</h2>
                    <p class="text-sm text-gray-500">{{ $payment->payment_date->format('d M Y') }}</p>
                </div>
                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full 
                    {{ $payment->status === 'success' ? 'bg-green-100 text-green-800' :
        ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>

            <!-- Details -->
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Invoice</span>
                    <a href="{{ route('invoices.show', $payment->invoice) }}"
                        class="font-medium text-primary-600 hover:text-primary-700">
                        {{ $payment->invoice->invoice_number }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Client</span>
                    <span class="font-medium">{{ $payment->invoice->client->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode Pembayaran</span>
                    <span class="font-medium">{{ ucfirst($payment->payment_method) }}</span>
                </div>
                @if($payment->reference_number)
                    <div class="flex justify-between">
                        <span class="text-gray-600">No. Referensi</span>
                        <span class="font-medium">{{ $payment->reference_number }}</span>
                    </div>
                @endif
                <div class="flex justify-between pt-4 border-t">
                    <span class="text-lg font-semibold text-gray-900">Jumlah</span>
                    <span class="text-lg font-bold text-green-600">Rp
                        {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($payment->notes)
                <div class="mt-6 pt-6 border-t">
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Catatan</h4>
                    <p class="text-gray-600">{{ $payment->notes }}</p>
                </div>
            @endif

            @if($payment->proof_of_payment)
                <div class="mt-6 pt-6 border-t">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</h4>
                    <img src="{{ asset('storage/' . $payment->proof_of_payment) }}" class="rounded-lg max-w-sm"
                        alt="Bukti Pembayaran">
                </div>
            @endif

            <div class="flex gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('payments.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Kembali</a>
                <a href="{{ route('invoices.show', $payment->invoice) }}"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Lihat Invoice</a>
            </div>
        </div>
    </div>
@endsection
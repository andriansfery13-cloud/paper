@extends('layouts.app')

@section('title', 'Pembayaran')
@section('header', 'Konfirmasi Pembayaran')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Order Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Paket</span>
                    <span class="font-medium">{{ $plan->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Periode</span>
                    <span class="font-medium">{{ $billingPeriod === 'yearly' ? 'Tahunan (12 bulan)' : 'Bulanan' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Order ID</span>
                    <span class="font-mono text-sm">{{ $orderId }}</span>
                </div>
                <hr>
                <div class="flex justify-between text-lg font-bold">
                    <span>Total</span>
                    <span class="text-primary-600">Rp {{ number_format($price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Button -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Siap Melakukan Pembayaran?</h3>
                <p class="text-gray-500 text-sm mb-6">Klik tombol di bawah untuk memilih metode pembayaran</p>

                <button id="pay-button"
                    class="w-full px-6 py-4 bg-primary-600 text-white rounded-xl font-semibold text-lg hover:bg-primary-700 transition-colors">
                    Bayar Sekarang
                </button>

                <p class="mt-4 text-xs text-gray-400">
                    Pembayaran diproses oleh Midtrans. Data Anda aman dan terenkripsi.
                </p>
            </div>
        </div>

        <!-- Back Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('subscription.pricing') }}" class="text-gray-600 hover:text-gray-900">
                ← Kembali ke halaman paket
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    window.location.href = '{{ route("subscription.finish") }}?order_id={{ $orderId }}';
                },
                onPending: function (result) {
                    window.location.href = '{{ route("subscription.finish") }}?order_id={{ $orderId }}';
                },
                onError: function (result) {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    console.error(result);
                },
                onClose: function () {
                    console.log('Payment popup closed');
                }
            });
        });
    </script>
@endpush
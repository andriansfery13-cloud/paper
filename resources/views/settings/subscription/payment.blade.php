@extends('layouts.app')

@section('title', 'Pembayaran Paket ' . $plan->name)
@section('header', 'Pembayaran Langganan')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 border-b bg-gray-50">
                <h3 class="text-xl font-semibold text-gray-900">Detail Pembayaran</h3>
            </div>

            <div class="p-6">
                <!-- Plan Summary -->
                <div class="bg-primary-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $plan->name }}</h4>
                            <p class="text-sm text-gray-600">Berlangganan 1 Bulan</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-primary-600">Rp
                                {{ number_format($plan->price_monthly, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-primary-200 pt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span>Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-2">
                            <span class="text-gray-600">PPN (0%)</span>
                            <span>Rp 0</span>
                        </div>
                        <div class="flex justify-between font-bold mt-2 pt-2 border-t border-primary-200">
                            <span>Total Pembayaran</span>
                            <span class="text-primary-600">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="mb-6">
                    <h5 class="font-medium text-gray-900 mb-3">Fitur yang didapat:</h5>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            {{ $plan->max_clients == -1 ? 'Unlimited' : $plan->max_clients }} Client
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            {{ $plan->max_invoices == -1 ? 'Unlimited' : $plan->max_invoices }} Invoice/bulan
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            {{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }} User
                        </li>
                        @if($plan->has_wa_gateway)
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                WhatsApp Gateway
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Payment Button -->
                <div class="space-y-3">
                    <button id="pay-button"
                        class="w-full px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-lg transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        Bayar Sekarang
                    </button>

                    <a href="{{ route('settings.subscription') }}"
                        class="block w-full text-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                        Batal
                    </a>
                </div>

                <p class="text-xs text-gray-500 text-center mt-4">
                    Pembayaran diproses secara aman melalui Midtrans
                </p>
            </div>
        </div>
    </div>

    <!-- Midtrans Snap JS -->
    @if($isProduction)
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
    @endif
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    window.location.href = '{{ route('settings.subscription.payment.callback', $plan) }}?success=1&order_id=' + result.order_id + '&transaction_status=' + result.transaction_status;
                },
                onPending: function (result) {
                    alert('Pembayaran sedang diproses. Silakan selesaikan pembayaran.');
                },
                onError: function (result) {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    window.location.href = '{{ route('settings.subscription') }}';
                },
                onClose: function () {
                    console.log('Payment popup closed');
                }
            });
        });
    </script>
@endsection
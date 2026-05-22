@extends('layouts.app')

@section('title', 'Pilih Paket Langganan')
@section('header', 'Pilih Paket Langganan')

@section('content')
    <!-- Current Plan Info -->
    @if($currentPlan && $tenant)
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600">Paket Saat Ini</p>
                    <p class="text-lg font-bold text-blue-800">{{ $currentPlan->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-600">Berlaku Hingga</p>
                    <p class="font-semibold text-blue-800">
                        {{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d M Y') : 'Trial' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Billing Toggle -->
    <div class="flex justify-center mb-8" x-data="{ yearly: false }">
        <div class="bg-gray-100 p-1 rounded-lg inline-flex">
            <button @click="yearly = false" :class="!yearly ? 'bg-white shadow' : ''"
                class="px-4 py-2 rounded-md text-sm font-medium transition-all">
                Bulanan
            </button>
            <button @click="yearly = true" :class="yearly ? 'bg-white shadow' : ''"
                class="px-4 py-2 rounded-md text-sm font-medium transition-all">
                Tahunan <span class="text-green-600 text-xs">Hemat 17%</span>
            </button>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" x-data="{ yearly: false }">
        @foreach($plans as $plan)
            <div
                class="bg-white rounded-xl shadow-lg border {{ $plan->slug === 'professional' ? 'border-primary-500 ring-2 ring-primary-500' : 'border-gray-200' }} overflow-hidden flex flex-col">
                @if($plan->slug === 'professional')
                    <div class="bg-primary-600 text-white text-center py-2 text-sm font-medium">
                        🔥 Paling Populer
                    </div>
                @endif

                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                    <p class="text-gray-500 text-sm mt-1">{{ $plan->description ?? 'Paket ' . $plan->name }}</p>

                    <!-- Monthly Price -->
                    <div class="mt-4" x-show="!yearly">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-bold text-gray-900">
                                {{ $plan->price_monthly > 0 ? 'Rp ' . number_format($plan->price_monthly / 1000, 0) . 'K' : 'Gratis' }}
                            </span>
                            @if($plan->price_monthly > 0)
                                <span class="text-gray-500 ml-1">/bulan</span>
                            @endif
                        </div>
                    </div>

                    <!-- Yearly Price -->
                    <div class="mt-4" x-show="yearly" x-cloak>
                        <div class="flex items-baseline">
                            @php $yearlyPrice = $plan->price_yearly ?: $plan->price_monthly * 10; @endphp
                            <span class="text-3xl font-bold text-gray-900">
                                {{ $yearlyPrice > 0 ? 'Rp ' . number_format($yearlyPrice / 1000, 0) . 'K' : 'Gratis' }}
                            </span>
                            @if($yearlyPrice > 0)
                                <span class="text-gray-500 ml-1">/tahun</span>
                            @endif
                        </div>
                        @if($plan->price_monthly > 0)
                            <p class="text-sm text-green-600 mt-1">
                                Hemat Rp {{ number_format(($plan->price_monthly * 12 - $yearlyPrice) / 1000, 0) }}K
                            </p>
                        @endif
                    </div>

                    <!-- Features -->
                    <ul class="mt-6 space-y-3 flex-1">
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ $plan->max_invoices == -1 ? 'Unlimited' : $plan->max_invoices }} Invoice
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ $plan->max_clients == -1 ? 'Unlimited' : $plan->max_clients }} Client
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }} User
                        </li>
                        @if($plan->has_payment_gateway)
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Payment Gateway
                            </li>
                        @endif
                        @if($plan->has_api_access)
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                API Access
                            </li>
                        @endif
                    </ul>

                    <!-- CTA Button -->
                    <div class="mt-6">
                        @if($currentPlan && $currentPlan->id === $plan->id)
                            <button disabled
                                class="w-full px-4 py-3 bg-gray-100 text-gray-500 rounded-lg font-medium cursor-not-allowed">
                                Paket Saat Ini
                            </button>
                        @else
                            <form action="{{ route('subscription.checkout') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <input type="hidden" name="billing_period" x-bind:value="yearly ? 'yearly' : 'monthly'">
                                <button type="submit"
                                    class="w-full px-4 py-3 {{ $plan->slug === 'professional' ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-800 hover:bg-gray-900' }} text-white rounded-lg font-medium transition-colors">
                                    {{ $plan->price_monthly > 0 ? 'Pilih Paket' : 'Mulai Gratis' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- FAQ Section -->
    <div class="mt-12 bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Pertanyaan Umum</h2>
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-gray-800">Bagaimana cara upgrade paket?</h3>
                <p class="text-gray-600 text-sm mt-1">Pilih paket yang diinginkan dan lakukan pembayaran. Paket akan
                    langsung aktif setelah pembayaran berhasil.</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Apakah sisa waktu langganan akan hangus?</h3>
                <p class="text-gray-600 text-sm mt-1">Tidak, sisa waktu langganan akan ditambahkan ke paket baru Anda.</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Metode pembayaran apa saja yang tersedia?</h3>
                <p class="text-gray-600 text-sm mt-1">Kami menerima pembayaran via Bank Transfer, E-Wallet (GoPay, OVO,
                    Dana), Kartu Kredit/Debit, dan QRIS.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Sync yearly toggle across all instances
        document.addEventListener('alpine:init', () => {
            Alpine.store('billing', { yearly: false });
        });
    </script>
@endpush
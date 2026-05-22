@extends('layouts.app')

@section('title', 'Langganan & Token')
@section('header', 'Langganan & Token')

@section('content')
    <div class="space-y-6">
        <!-- Plan Info & Token Balance -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Current Plan -->
            <div class="bg-white rounded-xl shadow-sm border p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-32 h-32 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                        </path>
                    </svg>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-1">Paket Langganan</h3>
                <p class="text-sm text-gray-500 mb-6">Status paket Anda saat ini</p>

                <div class="space-y-4 relative z-10">
                    <div>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $tenant->isSubscriptionValid() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $tenant->currentPlan->name ?? 'Tidak Ada Paket' }}
                        </span>
                        @if($tenant->isOnTrial())
                            <span class="ml-2 text-xs font-medium text-orange-600">Trial</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Status</span>
                        <span
                            class="font-medium {{ $tenant->isActive() ? 'text-green-600' : 'text-red-600' }}">{{ $tenant->status == 'active' ? 'Aktif' : 'Non-Aktif' }}</span>
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Berakhir Pada</span>
                        <span class="font-medium text-gray-900">
                            @if($tenant->isOnTrial() && $tenant->trial_ends_at)
                                {{ $tenant->trial_ends_at->format('d M Y') }} ({{ $tenant->daysUntilExpiry() }} hari lagi)
                            @elseif($tenant->hasActiveSubscription() && $tenant->subscription_ends_at)
                                {{ $tenant->subscription_ends_at->format('d M Y') }} ({{ $tenant->daysUntilExpiry() }} hari
                                lagi)
                            @else
                                -
                            @endif
                        </span>
                    </div>

                    <div class="pt-4 border-t">
                        <p class="text-xs text-gray-500">Hubungi admin untuk upgrade paket.</p>
                    </div>
                </div>
            </div>

            <!-- Token Balance -->
            <div class="bg-white rounded-xl shadow-sm border p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-32 h-32 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.15-1.46-3.27-3.4h1.96c.1 1.05 1.18 1.91 2.53 1.91 1.29 0 2.13-.77 2.13-2.11 0-2.85-4.54-2.81-4.54-5.45 0-1.63 1.29-2.84 2.9-3.19V4h2.93v1.9c1.59.38 2.89 1.45 3.09 3.29h-1.97c-.12-.9-1.14-1.7-2.38-1.7-1.25 0-2.05.81-2.05 1.97 0 2.8 4.6 2.68 4.6 5.69 0 1.68-1.3 2.89-2.96 3.23z" />
                    </svg>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-1">Saldo Token</h3>
                <p class="text-sm text-gray-500 mb-6">Digunakan untuk fitur premium</p>

                <div class="relative z-10">
                    <div class="flex items-baseline mb-2">
                        <span class="text-4xl font-bold text-gray-900">{{ number_format($tenant->token_balance) }}</span>
                        <span class="ml-2 text-sm text-gray-500">token</span>
                    </div>

                    <p class="text-sm text-gray-600 mb-6">
                        Token dapat digunakan untuk akses fitur AI, Blast WhatsApp, dan fitur tambahan lainnya.
                    </p>

                    <div class="pt-4 border-t">
                        <button disabled
                            class="w-full px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed text-sm font-medium">
                            Top Up Token (Segera Hadir)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Plans -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-900">Pilih Paket Langganan</h3>
                <p class="text-sm text-gray-500 mt-1">Upgrade paket Anda untuk akses fitur lebih lengkap</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($plans as $plan)
                        <div
                            class="relative border rounded-xl p-6 hover:shadow-lg transition-shadow {{ $tenant->current_plan_id == $plan->id ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }}">
                            @if($tenant->current_plan_id == $plan->id)
                                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-600 text-white">
                                        Paket Aktif
                                    </span>
                                </div>
                            @endif

                            <div class="text-center mb-6">
                                <h4 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h4>
                                <div class="mt-2">
                                    <span class="text-3xl font-bold text-gray-900">Rp
                                        {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                                    <span class="text-gray-500">/bulan</span>
                                </div>
                            </div>

                            <ul class="space-y-3 mb-6">
                                <li class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $plan->max_clients == -1 ? 'Unlimited' : $plan->max_clients }} Client
                                </li>
                                <li class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $plan->max_invoices == -1 ? 'Unlimited' : $plan->max_invoices }} Invoice/bulan
                                </li>
                                <li class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }} User
                                </li>
                                @if($plan->has_wa_gateway)
                                    <li class="flex items-center text-sm text-gray-600">
                                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        WhatsApp Gateway
                                    </li>
                                @endif
                                @if($plan->has_custom_template)
                                    <li class="flex items-center text-sm text-gray-600">
                                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Template Kustom
                                    </li>
                                @endif
                            </ul>

                            @if($tenant->current_plan_id == $plan->id)
                                <button disabled
                                    class="w-full px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed font-medium">
                                    Paket Saat Ini
                                </button>
                            @else
                                <form action="{{ route('settings.subscription.purchase', $plan) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium transition-colors">
                                        @if($plan->price_monthly == 0)
                                            Pilih Paket Gratis
                                        @else
                                            Beli Paket
                                        @endif
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-12 text-gray-500">
                            Belum ada paket langganan yang tersedia.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Usage Limits -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-900">Limitasi Penggunaan</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Usage Item -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">Client</span>
                            <span class="text-xs text-gray-500">
                                {{ $tenant->clients()->count() }} /
                                {{ $tenant->currentPlan->max_clients == -1 ? 'Unlimited' : $tenant->currentPlan->max_clients }}
                            </span>
                        </div>
                        @php
                            $clientPercent = $tenant->currentPlan->max_clients > 0 ? ($tenant->clients()->count() / $tenant->currentPlan->max_clients) * 100 : 0;
                            if ($tenant->currentPlan->max_clients == -1)
                                $clientPercent = 5;
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($clientPercent, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Usage Item -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">Invoice (Bulanan)</span>
                            <span class="text-xs text-gray-500">
                                {{ $tenant->invoices()->count() }} /
                                {{ $tenant->currentPlan->max_invoices == -1 ? 'Unlimited' : $tenant->currentPlan->max_invoices }}
                            </span>
                        </div>
                        @php
                            $invoicePercent = $tenant->currentPlan->max_invoices > 0 ? ($tenant->invoices()->count() / $tenant->currentPlan->max_invoices) * 100 : 0;
                            if ($tenant->currentPlan->max_invoices == -1)
                                $invoicePercent = 5;
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min($invoicePercent, 100) }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Usage Item -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">User</span>
                            <span class="text-xs text-gray-500">
                                {{ $tenant->users()->count() }} /
                                {{ $tenant->currentPlan->max_users == -1 ? 'Unlimited' : $tenant->currentPlan->max_users }}
                            </span>
                        </div>
                        @php
                            $userPercent = $tenant->currentPlan->max_users > 0 ? ($tenant->users()->count() / $tenant->currentPlan->max_users) * 100 : 0;
                            if ($tenant->currentPlan->max_users == -1)
                                $userPercent = 5;
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($userPercent, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent History -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900">Riwayat Langganan Terakhir</h3>
                <a href="{{ route('settings.subscription.history') }}"
                    class="text-sm text-primary-600 hover:text-primary-700 hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase font-medium">
                        <tr>
                            <th class="px-6 py-3 text-left">Paket</th>
                            <th class="px-6 py-3 text-left">Tanggal</th>
                            <th class="px-6 py-3 text-right">Biaya</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($histories as $history)
                            <tr class="hover:bg-gray-50 text-sm">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $history->plan->name ?? 'Paket' }}</td>
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
                                            Menunggu Pembayaran
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
        </div>
    </div>
@endsection

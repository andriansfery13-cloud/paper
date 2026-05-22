<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Kwitansi - {{ $receipt->receipt_number }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <!-- Header Status -->
            <div class="bg-green-600 p-6 text-center text-white">
                <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Dokumen Valid</h1>
                <p class="text-green-100 mt-1">Kwitansi ini resmi dan valid.</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Document Info -->
                <div class="text-center">
                    <p class="text-sm text-gray-500 uppercase tracking-widest mb-1">Nominal Pembayaran</p>
                    <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($receipt->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 mb-1">Nomor Kwitansi</p>
                        <p class="font-semibold text-gray-900">{{ $receipt->receipt_number }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 mb-1">Tanggal Bayar</p>
                        <p class="font-semibold text-gray-900">{{ $receipt->receipt_date->format('d M Y') }}</p>
                    </div>
                </div>

                <!-- Parties -->
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500 font-bold uppercase">Penerima Dana</p>
                            @php
                                $tenant = $receipt->invoice ? $receipt->invoice->tenant : null;
                            @endphp
                            <p class="text-gray-900 font-medium">
                                {{ $tenant ? $tenant->company_name : 'Tidak Diketahui' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500 font-bold uppercase">Pembayar</p>
                            @php
                                $client = $receipt->invoice ? $receipt->invoice->client : null;
                            @endphp
                            <p class="text-gray-900 font-medium">{{ $client ? $client->name : 'Tidak Diketahui' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Reference Info -->
                <div class="bg-gray-50 p-4 rounded-lg text-sm">
                    <p class="text-gray-500 mb-1">Untuk Pembayaran</p>
                    <p class="font-medium text-gray-900">
                        Invoice {{ $receipt->invoice->invoice_number }}
                    </p>
                    <p class="text-gray-500 mt-2 mb-1">Metode Pembayaran</p>
                    <p class="font-medium text-gray-900 uppercase">
                        {{ $receipt->payment->payment_method ?? 'Lainnya' }}
                    </p>
                </div>

                <div class="pt-4 border-t">
                    <a href="{{ route('verify.receipt.pdf', $receipt->verification_code) }}"
                        class="flex items-center justify-center w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Kwitansi
                    </a>
                </div>

                <div class="text-center pt-4">
                    <p class="text-xs text-gray-400">Diverifikasi oleh Sistem Paperly</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
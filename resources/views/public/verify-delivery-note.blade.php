<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Surat Jalan - {{ $deliveryNote->delivery_number }}</title>
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
            <div class="bg-blue-600 p-6 text-center text-white">
                <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Dokumen Valid</h1>
                <p class="text-blue-100 mt-1">Surat Jalan ini resmi dan terdaftar di sistem.</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Document Info -->
                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-500 uppercase tracking-widest mb-1">Nomor Surat Jalan</p>
                        <p class="text-xl font-bold text-gray-900">{{ $deliveryNote->delivery_number }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 mb-1">Tanggal Kirim</p>
                        <p class="font-semibold text-gray-900">{{ $deliveryNote->delivery_date->format('d M Y') }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 mb-1">Jumlah Item</p>
                        <p class="font-semibold text-gray-900">{{ $deliveryNote->items->count() }} Barang</p>
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
                            <p class="text-xs text-gray-500 font-bold uppercase">Pengirim</p>
                            @php
                                $tenant = $deliveryNote->invoice ? $deliveryNote->invoice->tenant : null;
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
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500 font-bold uppercase">Penerima</p>
                            <p class="text-gray-900 font-medium">{{ $deliveryNote->recipient_name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $deliveryNote->recipient_address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="flex justify-center pt-4 border-t">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium 
                        {{ $deliveryNote->status === 'delivered' ? 'bg-green-100 text-green-800' :
    ($deliveryNote->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        Status: {{ $deliveryNote->status_label }}
                    </span>
                </div>

                <div class="text-center pt-4">
                    <p class="text-xs text-gray-400">Diverifikasi oleh Sistem Paperly</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
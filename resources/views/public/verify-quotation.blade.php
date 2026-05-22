<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Quotation - {{ $quotation->quotation_number }}</title>
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
            <div class="bg-green-500 p-6 text-center text-white">
                <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Dokumen Valid</h1>
                <p class="text-green-100 mt-1">Quotation ini resmi dan terdaftar di sistem.</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Document Info -->
                <div class="text-center">
                    <p class="text-sm text-gray-500 uppercase tracking-widest mb-1">Total Penawaran</p>
                    <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($quotation->total, 0, ',', '.') }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 mb-1">Nomor Quotation</p>
                        <p class="font-semibold text-gray-900">{{ $quotation->quotation_number }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 mb-1">Tanggal</p>
                        <p class="font-semibold text-gray-900">
                            {{ $quotation->quotation_date ? $quotation->quotation_date->format('d M Y') : '-' }}
                        </p>
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
                            <p class="text-xs text-gray-500 font-bold uppercase">Penerbit</p>
                            <p class="text-gray-900 font-medium">{{ $quotation->tenant->company_name }}</p>
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
                            <p class="text-xs text-gray-500 font-bold uppercase">Kepada</p>
                            <p class="text-gray-900 font-medium">{{ $quotation->client->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="pt-4 border-t space-y-3">
                    <div class="flex justify-center">
                        @if($quotation->status == 'accepted')
                            <span
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Status: DITERIMA
                            </span>
                        @elseif($quotation->status == 'rejected')
                            <span
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                Status: DITOLAK
                            </span>
                        @elseif($quotation->status == 'sent')
                            <span
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                Status: TERKIRIM
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                Status: {{ strtoupper($quotation->status) }}
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('verify.quotation.pdf', $quotation->verification_code) }}"
                        class="flex items-center justify-center w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Quotation
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
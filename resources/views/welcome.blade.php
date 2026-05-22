<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paperly - Software Invoice & Pembayaran Bisnis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        // Paper.id inspired Green Palette
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        secondary: {
                            50: '#f8fafc',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-white font-sans text-secondary-900">
    <!-- Navbar -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-md z-50 border-b border-gray-100 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold text-2xl">
                        P
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-gray-900">Paperly</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#solusi"
                        class="text-gray-600 hover:text-primary-600 font-medium transition-colors">Solusi</a>
                    <a href="#features"
                        class="text-gray-600 hover:text-primary-600 font-medium transition-colors">Fitur</a>
                    <a href="#pricing"
                        class="text-gray-600 hover:text-primary-600 font-medium transition-colors">Harga</a>
                    <a href="#blog" class="text-gray-600 hover:text-primary-600 font-medium transition-colors">Blog</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}"
                        class="text-gray-700 hover:text-primary-600 font-semibold transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-5 py-2.5 bg-primary-600 text-white font-semibold rounded-full hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30">
                        Daftar Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="text-center lg:text-left">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-50 border border-green-100 text-primary-700 font-medium text-sm mb-6">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        Solusi Pembayaran B2B Terbaik
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                        Kelola Invoice & <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-emerald-500">
                            Terima Pembayaran
                        </span>
                        <br>Lebih Cepat
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Satu platform untuk membuat invoice profesional, mengirim tagihan via WhatsApp, dan menerima
                        pembayaran dari berbagai metode secara otomatis.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('register') }}"
                            class="px-8 py-4 bg-primary-600 text-white font-bold rounded-full hover:bg-primary-700 transition-all shadow-xl shadow-primary-500/30 transform hover:-translate-y-1">
                            Mulai Gratis Sekarang
                        </a>
                        <a href="#demo"
                            class="px-8 py-4 bg-white text-gray-700 font-bold rounded-full border border-gray-200 hover:border-primary-500 hover:text-primary-600 transition-all">
                            Lihat Demo
                        </a>
                    </div>
                    <p class="mt-6 text-sm text-gray-500 flex items-center justify-center lg:justify-start gap-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Tanpa Kartu Kredit
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Gratis Selamanya*
                        </span>
                    </p>
                </div>

                <!-- visual -->
                <div class="relative lg:block">
                    <div
                        class="relative z-10 bg-white rounded-2xl shadow-2xl border border-gray-100 p-2 transform rotate-1 hover:rotate-0 transition-transform duration-500">
                        <div
                            class="bg-gray-50 rounded-xl overflow-hidden aspect-[4/3] flex items-center justify-center relative">
                            <!-- Abstract UI Representation -->
                            <div
                                class="absolute inset-x-8 top-8 bg-white rounded-t-xl shadow-sm border border-gray-200 h-full p-6">
                                <div class="flex justify-between items-center mb-6">
                                    <div class="h-4 w-24 bg-gray-200 rounded"></div>
                                    <div class="h-8 w-8 bg-green-100 rounded-full"></div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div class="h-24 bg-green-50 rounded-lg border border-green-100 p-4">
                                        <div class="h-3 w-12 bg-green-200 rounded mb-2"></div>
                                        <div class="h-6 w-20 bg-green-600 rounded"></div>
                                    </div>
                                    <div class="h-24 bg-gray-50 rounded-lg nav border border-gray-100 p-4"></div>
                                    <div class="h-24 bg-gray-50 rounded-lg border border-gray-100 p-4"></div>
                                </div>
                                <div class="space-y-3">
                                    <div class="h-12 bg-gray-50 rounded-lg w-full"></div>
                                    <div class="h-12 bg-gray-50 rounded-lg w-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Decor -->
                    <div
                        class="absolute -top-10 -right-10 w-64 h-64 bg-primary-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob">
                    </div>
                    <div
                        class="absolute -bottom-10 -left-10 w-64 h-64 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By -->
    <section class="py-10 border-y border-gray-100 bg-gray-50/50">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-8">Dipercaya oleh 10.000+ Bisnis
                di Indonesia</p>
            <div
                class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                <!-- Placeholders -->
                <div class="text-xl font-bold font-serif text-gray-800">Ullamco</div>
                <div class="text-xl font-bold font-sans text-gray-800">Logipsum</div>
                <div class="text-xl font-bold font-mono text-gray-800">NextGen</div>
                <div class="text-xl font-bold text-gray-800">AlphaCorp</div>
                <div class="text-xl font-bold font-serif text-gray-800">Omega</div>
            </div>
        </div>
    </section>

    <!-- Solutions -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Solusi Lengkap Bisnis Anda</h2>
                <p class="text-xl text-gray-600">Satu aplikasi untuk berbagai kebutuhan transaksi bisnis</p>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                <!-- Card 1 -->
                <div
                    class="group p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:border-primary-100 transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Invoicing Digital</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Buat dan kirim invoice profesional dalam hitungan detik. Tersedia template siap pakai dan
                        pengiriman via WhatsApp.
                    </p>
                    <a href="#"
                        class="text-primary-600 font-medium hover:text-primary-700 inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                        Pelajari Invoice <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>

                <!-- Card 2 -->
                <div
                    class="group p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:border-primary-100 transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Terima Pembayaran</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Sediakan opsi pembayaran beragam untuk klien: Transfer Bank, Kartu Kredit, QRIS, hingga
                        E-Wallet. Rekonsiliasi otomatis.
                    </p>
                    <a href="#"
                        class="text-blue-600 font-medium hover:text-blue-700 inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                        Pelajari Pembayaran <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>

                <!-- Card 3 -->
                <div
                    class="group p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:border-primary-100 transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Laporan Keuangan</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Monitor arus kas, laba rugi, dan neraca secara real-time. Ambil keputusan bisnis berdasarkan
                        data yang akurat.
                    </p>
                    <a href="#"
                        class="text-purple-600 font-medium hover:text-purple-700 inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                        Lihat Laporan <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Pilihan Paket Transparan</h2>
                <p class="text-xl text-gray-600">Mulai dari yang gratis hingga skala enterprise</p>

                <!-- Toggle (Visual) -->
                <div class="mt-8 flex justify-center items-center gap-3">
                    <span class="text-sm font-medium text-gray-900">Bulanan</span>
                    <button
                        class="relative w-12 h-6 bg-primary-600 rounded-full cursor-pointer transition-colors focus:outline-none">
                        <span
                            class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform transform translate-x-6"></span>
                    </button>
                    <span class="text-sm font-medium text-gray-500">Tahunan <span
                            class="text-green-600 text-xs font-bold bg-green-100 px-2 py-0.5 rounded-full ml-1">Hemat
                            20%</span></span>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 items-start">
                <!-- Free Tier -->
                <div
                    class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden hover:shadow-lg transition-all">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Paperly Free</h3>
                        <p class="text-sm text-gray-500 mt-1">Untuk UMKM yang baru mulai</p>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-extrabold text-gray-900">Gratis</span>
                        <span class="text-gray-500">/ selamanya</span>
                    </div>
                    <a href="{{ route('register') }}"
                        class="block w-full py-3 px-4 bg-primary-50 text-primary-700 font-bold text-center rounded-xl hover:bg-primary-100 transition-colors">
                        Daftar Sekarang
                    </a>
                    <ul class="mt-8 space-y-4 text-sm text-gray-600">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Invoice & Kwitansi Digital
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Terima Pembayaran (QRIS, VA)
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Laporan Keuangan Dasar
                        </li>
                    </ul>
                </div>

                <!-- Pro Tier (Popular) -->
                <div
                    class="bg-white rounded-3xl p-8 border-2 border-primary-500 shadow-xl relative overflow-hidden transform lg:-translate-y-4 hover:-translate-y-6 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 bg-primary-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl">
                        POPULAR
                    </div>
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Paperly Jet</h3>
                        <p class="text-sm text-gray-500 mt-1">Untuk bisnis yang berkembang</p>
                    </div>
                    <div class="mb-6">
                        <p class="text-xs text-gray-400 line-through">Rp 149.000</p>
                        <span class="text-4xl font-extrabold text-gray-900">Rp 99rb</span>
                        <span class="text-gray-500">/ bulan</span>
                    </div>
                    <a href="{{ route('register') }}"
                        class="block w-full py-3 px-4 bg-primary-600 text-white font-bold text-center rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/30">
                        Coba Gratis 14 Hari
                    </a>
                    <ul class="mt-8 space-y-4 text-sm text-gray-600">
                        <li class="flex items-center gap-3">
                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Semua Fitur Free</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Hapus Watermark Paperly
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Kirim Invoice via WhatsApp (Otomatis)
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Kustomisasi Template Dokumen
                        </li>
                    </ul>
                </div>

                <!-- Enterprise -->
                <div
                    class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden hover:shadow-lg transition-all">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Paperly Rocket</h3>
                        <p class="text-sm text-gray-500 mt-1">Skala besar & custom</p>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-extrabold text-gray-900">Hubungi Kami</span>
                    </div>
                    <a href="#"
                        class="block w-full py-3 px-4 bg-white border-2 border-gray-200 text-gray-700 font-bold text-center rounded-xl hover:border-primary-500 hover:text-primary-600 transition-colors">
                        Kontak Sales
                    </a>
                    <ul class="mt-8 space-y-4 text-sm text-gray-600">
                        <li class="flex items-center gap-3">
                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Semua Fitur Jet</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Manajemen Stok & Gudang
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            API Integration
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Dedicated Account Manager
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Pre-Footer / CTA -->
    <section class="py-24 bg-primary-900 text-white overflow-hidden relative">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10">
        </div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl md:text-5xl font-bold mb-6">Mulai Transformasi Bisnis Anda</h2>
            <p class="text-primary-100 text-xl mb-10 max-w-2xl mx-auto">
                Bergabunglah dengan ribuan pengusaha yang telah mendigitalkan keuangan bisnis mereka. Gratis untuk
                memulai.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}"
                    class="px-10 py-4 bg-white text-primary-900 font-bold rounded-full hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl">
                    Daftar Gratis Sekarang
                </a>
                <a href="#"
                    class="px-10 py-4 bg-transparent border-2 border-primary-500 text-white font-bold rounded-full hover:bg-primary-800 transition-all">
                    Hubungi Sales
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-50 pt-20 pb-10 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-6">
                        <div
                            class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                            P</div>
                        <span class="text-xl font-bold text-gray-900">Paperly</span>
                    </div>
                    <p class="text-gray-500 mb-6">
                        Platform invoice dan pembayaran B2B termudah untuk UMKM Indonesia.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="text-gray-400 hover:text-primary-600 transition-colors"><span
                                class="sr-only">Facebook</span><svg class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd"></path>
                            </svg></a>
                        <a href="#" class="text-gray-400 hover:text-primary-600 transition-colors"><span
                                class="sr-only">Instagram</span><svg class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.047-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.067-.06-1.407-.06-4.123v-.08c0-2.643.013-2.987.06-4.043.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465 1.067-.047 1.407-.06 4.123-.06h.08zm-4.28 9.029a4.105 4.105 0 108.21 0 4.105 4.105 0 00-8.21 0zm4.386-3.903a.563.563 0 100-1.126.563.563 0 000 1.126z"
                                    clip-rule="evenodd"></path>
                            </svg></a>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-gray-900 mb-6">Produk</h4>
                    <ul class="space-y-4 text-gray-600">
                        <li><a href="#" class="hover:text-primary-600">Invoicing</a></li>
                        <li><a href="#" class="hover:text-primary-600">Terima Pembayaran</a></li>
                        <li><a href="#" class="hover:text-primary-600">Kirim Pembayaran</a></li>
                        <li><a href="#" class="hover:text-primary-600">Accounting</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-gray-900 mb-6">Perusahaan</h4>
                    <ul class="space-y-4 text-gray-600">
                        <li><a href="#" class="hover:text-primary-600">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-primary-600">Karir</a></li>
                        <li><a href="#" class="hover:text-primary-600">Partner</a></li>
                        <li><a href="#" class="hover:text-primary-600">Hubungi Kami</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-gray-900 mb-6">Dukungan</h4>
                    <ul class="space-y-4 text-gray-600">
                        <li><a href="#" class="hover:text-primary-600">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-primary-600">Panduan</a></li>
                        <li><a href="#" class="hover:text-primary-600">Status API</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} CV Digtaf Jaya Inovasi. All rights reserved.</p>
                <div class="flex gap-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-primary-600">Privacy Policy</a>
                    <a href="#" class="hover:text-primary-600">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
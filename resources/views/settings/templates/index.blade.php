@extends('layouts.app')

@section('title', 'Template Dokumen')
@section('header', 'Template Dokumen')

@section('content')
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Template Dokumen</h2>
                <p class="text-gray-600">Pilih dan kelola tampilan dokumen bisnis Anda</p>
            </div>
            <a href="{{ route('settings.templates.create') }}"
                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Template Baru</span>
            </a>
        </div>

        <!-- Category Filter -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('settings.templates.index') }}"
                    class="{{ !$type ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Semua
                </a>
                <a href="{{ route('settings.templates.index', ['type' => 'invoice']) }}"
                    class="{{ $type == 'invoice' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Invoice
                </a>
                <a href="{{ route('settings.templates.index', ['type' => 'quotation']) }}"
                    class="{{ $type == 'quotation' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Quotation
                </a>
                <a href="{{ route('settings.templates.index', ['type' => 'receipt']) }}"
                    class="{{ $type == 'receipt' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Kwitansi
                </a>
                <a href="{{ route('settings.templates.index', ['type' => 'delivery_note']) }}"
                    class="{{ $type == 'delivery_note' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Surat Jalan
                </a>
            </nav>
        </div>

        <!-- My Templates -->
        @if($templates->count() > 0)
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Template Saya</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($templates as $template)
                        <div
                            class="group relative bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Thumbnail -->
                            <div class="aspect-w-4 aspect-h-3 bg-gray-100 relative">
                                <img src="{{ $template->thumbnail ?? 'https://placehold.co/400x300/e2e8f0/475569?text=' . urlencode($template->name) }}"
                                    alt="{{ $template->name }}" class="w-full h-48 object-cover">
                                <div class="absolute top-2 right-2">
                                    @if($template->is_default)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-sm">
                                            Default
                                        </span>
                                    @endif
                                </div>
                                <!-- Hover Overlay -->
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-opacity flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <div class="space-x-2">
                                        <a href="{{ route('settings.templates.preview', $template->id) }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-gray-900 hover:bg-gray-800">
                                            Preview
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 line-clamp-1">{{ $template->name }}</h4>
                                        <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $template->type) }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('settings.templates.edit', $template) }}"
                                            class="text-gray-400 hover:text-primary-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('settings.templates.destroy', $template) }}" method="POST"
                                            onsubmit="return confirm('Hapus template ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    @if(!$template->is_default)
                                        <form action="{{ route('settings.templates.use', $template->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                                Gunakan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-sm font-medium text-green-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Aktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- System Templates -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Template Sistem (Bawaan)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($systemTemplates as $template)
                    <div
                        class="group relative bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Thumbnail -->
                        <div class="aspect-w-4 aspect-h-3 bg-gray-100 relative">
                            <img src="{{ $template->thumbnail ?? 'https://placehold.co/400x300/e2e8f0/475569?text=' . urlencode($template->name) }}"
                                alt="{{ $template->name }}" class="w-full h-48 object-cover">

                            <!-- Hover Overlay -->
                            <div
                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-opacity flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <div class="space-x-2">
                                    <a href="{{ route('settings.templates.preview', $template->id) }}" target="_blank"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-gray-900 hover:bg-gray-800">
                                        Preview
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-base font-semibold text-gray-900 line-clamp-1">{{ $template->name }}</h4>
                                    <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $template->type) }}</p>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <form action="{{ route('settings.templates.use', $template->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Gunakan Template
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($systemTemplates->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <p class="text-gray-500">Tidak ada template sistem yang ditemukan.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
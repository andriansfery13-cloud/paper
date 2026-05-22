@extends('layouts.superadmin')

@section('title', 'Manajemen Template Sistem')
@section('header', 'Template Sistem')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Template Sistem</h2>
                <p class="text-gray-600">Kelola template bawaan yang dapat digunakan oleh semua tenant</p>
            </div>
            <a href="{{ route('superadmin.templates.create') }}"
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
                <a href="{{ route('superadmin.templates.index') }}"
                    class="{{ !$type ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Semua
                </a>
                <a href="{{ route('superadmin.templates.index', ['type' => 'invoice']) }}"
                    class="{{ $type == 'invoice' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Invoice
                </a>
                <a href="{{ route('superadmin.templates.index', ['type' => 'quotation']) }}"
                    class="{{ $type == 'quotation' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Quotation
                </a>
                <a href="{{ route('superadmin.templates.index', ['type' => 'receipt']) }}"
                    class="{{ $type == 'receipt' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Kwitansi
                </a>
                <a href="{{ route('superadmin.templates.index', ['type' => 'delivery_note']) }}"
                    class="{{ $type == 'delivery_note' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Surat Jalan
                </a>
            </nav>
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($templates as $template)
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
                                <a href="{{ route('superadmin.templates.preview', $template->id) }}" target="_blank"
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
                                <a href="{{ route('superadmin.templates.edit', $template) }}"
                                    class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    Edit Template
                                </a>
                            </div>
                            <form action="{{ route('superadmin.templates.destroy', $template) }}" method="POST"
                                onsubmit="return confirm('Hapus template sistem ini? Tenant yang sudah menggunakan template ini tidak akan terpengaruh.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $templates->withQueryString()->links() }}
        </div>
    </div>
@endsection
@extends('layouts.superadmin')

@section('title', 'Buat Template Sistem Baru')
@section('header', 'Buat Template Baru')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('superadmin.templates.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Template</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Contoh: Modern Invoice Blue">
                        @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Dokumen</label>
                        <select name="type"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="invoice" {{ old('type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                            <option value="quotation" {{ old('type') == 'quotation' ? 'selected' : '' }}>Quotation</option>
                            <option value="receipt" {{ old('type') == 'receipt' ? 'selected' : '' }}>Kwitansi</option>
                            <option value="delivery_note" {{ old('type') == 'delivery_note' ? 'selected' : '' }}>Surat Jalan
                            </option>
                        </select>
                        @error('type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail URL</label>
                        <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="https://example.com/image.jpg">
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk menggunakan placeholder default</p>
                        @error('thumbnail_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">HTML Content</label>
                    <div class="border rounded-lg overflow-hidden">
                        <textarea name="html_content" id="html_content" rows="20"
                            class="w-full px-4 py-2 font-mono text-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="<!DOCTYPE html>...">{{ old('html_content') }}</textarea>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Gunakan HTML dan CSS inline. Variable Blade tersedia:
                        <code>{{ '$tenant' }}</code>, <code>{{ '$client' }}</code>, <code>{{ '$items' }}</code>, dll.</p>
                    @error('html_content')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('superadmin.templates.index') }}"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
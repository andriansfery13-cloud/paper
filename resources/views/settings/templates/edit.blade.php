@extends('layouts.app')

@section('title', 'Edit Template')
@section('header', 'Edit Template')

@section('content')
    <div class="max-w-6xl mx-auto">
        <form action="{{ route('settings.templates.update', $template) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Settings Column -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Template</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Template *</label>
                                <input type="text" name="name" value="{{ $template->name }}" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Dokumen</label>
                                <input type="text" value="{{ ucfirst(str_replace('_', ' ', $template->type)) }}" disabled
                                    class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-500">
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_default" value="1" id="is_default" {{ $template->is_default ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="is_default" class="ml-2 text-sm text-gray-700">Jadikan Default</label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h4 class="font-medium text-blue-800 mb-2">Variabel Tersedia</h4>
                        <p class="text-xs text-blue-600 mb-2">Gunakan variabel ini dalam HTML Anda:</p>
                        <div class="space-y-1 text-xs font-mono text-gray-600 max-h-60 overflow-y-auto">
                            <div class="bg-white px-2 py-1 rounded border">@{{ $invoice_number }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $date }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $due_date }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $company_name }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $client_name }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $items_table }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $subtotal }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $tax }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $total }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $notes }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $terms }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $qr_code }}</div>
                            <div class="bg-white px-2 py-1 rounded border">@{{ $signature }}</div>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('settings.templates.index') }}"
                            class="block w-full text-center mt-3 px-4 py-2 border rounded-lg hover:bg-gray-50 text-gray-600">
                            Batal
                        </a>
                    </div>
                </div>

                <!-- Editor Column -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border overflow-hidden h-full flex flex-col">
                        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">HTML Editor</h3>
                            <a href="{{ route('settings.templates.preview', $template->id) }}" target="_blank"
                                class="text-sm text-blue-600 hover:underline">Preview Hasil</a>
                        </div>
                        <div class="flex-grow p-0">
                            <textarea name="html_content"
                                class="w-full h-[600px] p-4 font-mono text-sm focus:outline-none focus:ring-0 border-0">{{ $template->html_content }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
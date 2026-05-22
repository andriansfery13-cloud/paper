@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('header', 'Tambah Produk Baru')

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-xl shadow-sm border p-6">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <!-- Nama -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- SKU -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Auto-generate jika kosong">
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Harga Beli -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                    <input type="number" name="purchase_price" value="{{ old('purchase_price', 0) }}" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Harga Jual -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="selling_price" value="{{ old('selling_price', 0) }}" min="0" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('selling_price') border-red-500 @enderror">
                    @error('selling_price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Stok Awal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
                    <input type="number" name="current_stock" value="{{ old('current_stock', 0) }}" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Min Stok -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stok</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Gambar -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Deskripsi -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('description') }}</textarea>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('products.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
            </div>
        </form>
    </div>
@endsection
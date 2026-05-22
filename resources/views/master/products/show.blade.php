@extends('layouts.app')

@section('title', 'Detail Produk')
@section('header', 'Detail Produk')

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-start gap-6">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="w-32 h-32 rounded-lg object-cover">
                @else
                    <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                @endif
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $product->name }}</h2>
                            <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                        </div>
                        <span
                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mt-6">
                        <div>
                            <p class="text-sm text-gray-500">Harga Jual</p>
                            <p class="text-xl font-bold text-primary-600">Rp
                                {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Harga Beli</p>
                            <p class="text-lg font-medium">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Stok</p>
                            <p
                                class="text-lg font-medium {{ $product->current_stock <= $product->min_stock ? 'text-red-600' : '' }}">
                                {{ number_format($product->current_stock, 0) }} {{ $product->unit }}
                            </p>
                        </div>
                    </div>

                    @if($product->description)
                        <div class="mt-4 pt-4 border-t">
                            <p class="text-sm text-gray-500 mb-1">Deskripsi</p>
                            <p class="text-gray-700">{{ $product->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('products.edit', $product) }}"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Edit</a>
                <a href="{{ route('products.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Kembali</a>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Tambah Hutang Supplier')
@section('header', 'Tambah Hutang Supplier')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Catat Hutang Baru</h3>
                <p class="text-sm text-gray-500">Isi form berikut untuk mencatat hutang ke supplier</p>
            </div>

            <form action="{{ route('finance.payables.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                        <select name="supplier_id" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('supplier_id') border-red-500 @enderror">
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi *</label>
                            <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}"
                                required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('transaction_date') border-red-500 @enderror">
                            @error('transaction_date')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo *</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}" required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('due_date') border-red-500 @enderror">
                            @error('due_date')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
                        <input type="text" name="description" value="{{ old('description') }}" required
                            placeholder="Contoh: Pembelian bahan baku"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror">
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Hutang *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="1"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('amount') border-red-500 @enderror">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" rows="3" placeholder="Catatan tambahan..."
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="p-6 border-t bg-gray-50 flex justify-end space-x-3">
                    <a href="{{ route('finance.payables.index') }}"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors">Batal</a>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
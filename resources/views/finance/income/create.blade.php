@extends('layouts.app')

@section('title', 'Tambah Pemasukan Manual')
@section('header', 'Tambah Pemasukan Manual')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Catat Pemasukan Manual</h3>
                <p class="text-sm text-gray-500">Untuk pemasukan selain dari pembayaran invoice</p>
            </div>

            <form action="{{ route('finance.income.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                            <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}"
                                required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('transaction_date') border-red-500 @enderror">
                            @error('transaction_date')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="1"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('amount') border-red-500 @enderror">
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
                        <input type="text" name="description" value="{{ old('description') }}" required
                            placeholder="Contoh: Penjualan cash produk X"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror">
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                            <select name="payment_method"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Metode</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>
                                    Kartu Kredit</option>
                                <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                                <option value="ewallet" {{ old('payment_method') == 'ewallet' ? 'selected' : '' }}>E-Wallet
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                            <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                                placeholder="Opsional"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" rows="3" placeholder="Catatan tambahan..."
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="p-6 border-t bg-gray-50 flex justify-end space-x-3">
                    <a href="{{ route('finance.income.index') }}"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors">Batal</a>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
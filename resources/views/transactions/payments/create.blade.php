@extends('layouts.app')

@section('title', 'Catat Pembayaran')
@section('header', 'Catat Pembayaran')

@section('content')
    <div class="max-w-2xl">
        <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-xl shadow-sm border p-6">
            @csrf

            <div class="space-y-6">
                <!-- Invoice Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice <span
                            class="text-red-500">*</span></label>
                    <select name="invoice_id" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('invoice_id') border-red-500 @enderror">
                        <option value="">Pilih Invoice</option>
                        @foreach($unpaidInvoices as $inv)
                            <option value="{{ $inv->id }}" {{ old('invoice_id', $invoice->id ?? '') == $inv->id ? 'selected' : '' }}>
                                {{ $inv->invoice_number }} - {{ $inv->client->name }} (Sisa: Rp
                                {{ number_format($inv->amount_due, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                @if($invoice)
                    <!-- Invoice Info -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Invoice</span>
                            <span class="font-medium">{{ $invoice->invoice_number }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Client</span>
                            <span class="font-medium">{{ $invoice->client->name }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Total</span>
                            <span class="font-medium">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t">
                            <span class="text-gray-900 font-medium">Sisa Tagihan</span>
                            <span class="font-bold text-red-600">Rp
                                {{ number_format($invoice->amount_due, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Payment Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $invoice->amount_due ?? '') }}" required
                        min="1"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('amount') border-red-500 @enderror">
                    @error('amount')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran <span
                            class="text-red-500">*</span></label>
                    <select name="payment_method" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank
                        </option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Cek/Giro</option>
                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Reference Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Contoh: Nomor transfer bank">
                </div>

                <!-- Proof of Payment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Pembayaran</label>
                    <input type="file" name="proof_of_payment" accept="image/*"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Max 2MB</p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('payments.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan
                    Pembayaran</button>
            </div>
        </form>
    </div>
@endsection
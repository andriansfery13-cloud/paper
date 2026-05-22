@extends('layouts.app')

@section('title', 'Buat Kwitansi')
@section('header', 'Buat Kwitansi')

@section('content')
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('receipts.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

            <!-- Invoice Info -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Invoice</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">No. Invoice</p>
                        <p class="font-medium text-primary-600">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Client</p>
                        <p class="font-medium">{{ $invoice->client->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Invoice</p>
                        <p class="font-medium">{{ $invoice->formatted_total }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Sudah Dibayar</p>
                        <p class="font-medium text-green-600">Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Sisa Tagihan</p>
                        <p class="text-2xl font-bold text-red-600">Rp {{ number_format($invoice->amount_due, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Receipt Form -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Pembayaran</h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kwitansi <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="receipt_number" value="{{ old('receipt_number', $nextNumber ?? '') }}"
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            @error('receipt_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kwitansi <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            @error('receipt_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran <span
                                    class="text-red-500">*</span></label>
                            <select name="payment_method" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer
                                    Bank</option>
                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Cek/Giro
                                </option>
                                <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                                <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Lainnya
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="amount" value="{{ old('amount', $invoice->amount_due) }}" min="1"
                                max="{{ $invoice->amount_due }}" required step="1"
                                class="w-full pl-12 pr-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Maksimal: Rp
                            {{ number_format($invoice->amount_due, 0, ',', '.') }}
                        </p>
                        @error('amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="No. Transfer / Cek / Referensi">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Auto Paid Notice -->
            <div class="bg-green-50 rounded-xl border border-green-200 p-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-green-800">Otomatis Update Invoice</h4>
                        <p class="text-sm text-green-700 mt-1">
                            Setelah kwitansi dibuat, invoice akan otomatis diupdate.
                            Jika pembayaran lunas, status invoice akan menjadi <strong>PAID</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Document Settings -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Dokumen</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="include_signature" value="1" checked
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                            <span class="ml-2 text-gray-700">Tampilkan Tanda Tangan</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1 ml-6">Tanda tangan digital akan ditampilkan di dokumen</p>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="include_stamp" value="1" checked
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                            <span class="ml-2 text-gray-700">Tampilkan Cap</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1 ml-6">Cap perusahaan akan ditampilkan di dokumen</p>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="include_qr" value="1" checked
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                            <span class="ml-2 text-gray-700">Tampilkan QR Code</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1 ml-6">QR Code untuk verifikasi dokumen akan ditampilkan</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('invoices.show', $invoice) }}"
                    class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Buat Kwitansi
                </button>
            </div>
        </form>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Buat Invoice')
@section('header', 'Buat Invoice Baru')

@section('content')
    <form action="{{ route('invoices.store') }}" method="POST" x-data="invoiceForm()" class="space-y-6">
        @csrf

        <!-- Header Info -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Invoice <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="invoice_number" value="{{ old('invoice_number', $nextNumber ?? '') }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    @error('invoice_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client <span
                            class="text-red-500">*</span></label>
                    <select name="client_id" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Pilih Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}"
                        required class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perihal</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Contoh: Pembayaran Jasa Konsultasi Bulan Januari">
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Item Invoice</h3>

            <table class="w-full mb-4">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Qty</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Unit</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">Harga</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">Subtotal</th>
                        <th class="px-3 py-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr class="border-b">
                            <td class="px-3 py-2">
                                <input type="text" :name="'items['+index+'][description]'" x-model="item.description"
                                    required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Nama item atau jasa">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="0.01"
                                    step="0.01" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 text-center"
                                    @input="calculateTotal()">
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" :name="'items['+index+'][unit]'" x-model="item.unit" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 text-center">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.unit_price"
                                    min="0" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 text-right"
                                    @input="calculateTotal()">
                            </td>
                            <td class="px-3 py-2 text-right font-medium"
                                x-text="formatRupiah(item.quantity * item.unit_price)"></td>
                            <td class="px-3 py-2">
                                <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700"
                                    x-show="items.length > 1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <button type="button" @click="addItem()"
                class="inline-flex items-center px-4 py-2 border border-dashed rounded-lg text-gray-600 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Item
            </button>

            <!-- Totals -->
            <div class="mt-6 pt-6 border-t">
                <div class="flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium" x-text="formatRupiah(subtotal)"></span>
                        </div>
                        <div class="flex justify-between pt-2 border-t">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-lg font-bold text-primary-600" x-text="formatRupiah(subtotal)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Catatan untuk client">{{ old('notes') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Syarat & Ketentuan</label>
                    <textarea name="terms" rows="3"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Syarat pembayaran">{{ old('terms', 'Pembayaran dilakukan melalui transfer bank.') }}</textarea>
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
            <a href="{{ route('invoices.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan
                Invoice</button>
        </div>
    </form>

    @push('scripts')
        <script>
            function invoiceForm() {
                return {
                    items: [{ description: '', quantity: 1, unit: 'pcs', unit_price: 0 }],
                    subtotal: 0,

                    addItem() {
                        this.items.push({ description: '', quantity: 1, unit: 'pcs', unit_price: 0 });
                    },

                    removeItem(index) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    },

                    calculateTotal() {
                        this.subtotal = this.items.reduce((sum, item) => {
                            return sum + (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                        }, 0);
                    },

                    formatRupiah(num) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(num || 0);
                    }
                }
            }
        </script>
    @endpush
@endsection
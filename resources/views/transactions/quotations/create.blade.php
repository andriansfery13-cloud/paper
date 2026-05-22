@extends('layouts.app')

@section('title', 'Buat Quotation')
@section('header', 'Buat Quotation Baru')

@section('content')
    <form action="{{ route('quotations.store') }}" method="POST" x-data="quotationForm()" class="space-y-6">
        @csrf

        <!-- Header Info -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Quotation <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="quotation_number" value="{{ old('quotation_number', $nextNumber) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    @error('quotation_number')
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
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Quotation <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="quotation_date" value="{{ old('quotation_date', date('Y-m-d')) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="valid_until"
                        value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perihal</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Contoh: Penawaran Jasa Konsultasi IT">
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Item Quotation</h3>

            <table class="w-full mb-4">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Qty</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Unit</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">Harga</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Pajak %</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">Subtotal</th>
                        <th class="px-3 py-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr class="border-b align-top">
                            <td class="px-3 py-2">
                                <div class="flex flex-col gap-2">
                                    <select x-model="item.product_id" @change="selectProduct(item)"
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm bg-gray-50">
                                        <option value="">-- Pilih Produk (Opsional) --</option>
                                        <template x-for="product in products" :key="product.id">
                                            <option :value="product.id"
                                                x-text="product.name + ' - ' + formatRupiah(product.selling_price)">
                                            </option>
                                        </template>
                                    </select>
                                    <input type="text" :name="'items['+index+'][description]'" x-model="item.description"
                                        required
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="Deskripsi item atau jasa">
                                    <input type="hidden" :name="'items['+index+'][product_id]'" x-model="item.product_id">
                                </div>
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
                            <td class="px-3 py-2">
                                <input type="number" :name="'items['+index+'][tax_percent]'" x-model="item.tax_percent"
                                    min="0" max="100" step="0.1"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 text-center"
                                    @input="calculateTotal()">
                            </td>
                            <td class="px-3 py-2 text-right font-medium pt-4" x-text="formatRupiah(getItemSubtotal(item))">
                            </td>
                            <td class="px-3 py-2 pt-3">
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

            <!-- Discount & Totals -->
            <div class="mt-6 pt-6 border-t">
                <div class="flex justify-end">
                    <div class="w-80 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium" x-text="formatRupiah(subtotal)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">PPN</span>
                            <span class="font-medium" x-text="formatRupiah(taxTotal)"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">Diskon</span>
                            <select name="discount_type" x-model="discountType" @change="calculateTotal()"
                                class="px-2 py-1 border rounded text-sm">
                                <option value="0">Nominal</option>
                                <option value="1">Persen</option>
                            </select>
                            <input type="number" name="discount_value" x-model="discountValue" min="0" step="0.01"
                                class="w-24 px-2 py-1 border rounded text-right text-sm" @input="calculateTotal()">
                            <span class="text-gray-500" x-text="discountType == 1 ? '%' : ''"></span>
                        </div>
                        <div class="flex justify-between items-center text-red-600" x-show="discountAmount > 0">
                            <span>Potongan</span>
                            <span class="font-medium" x-text="'- ' + formatRupiah(discountAmount)"></span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-lg font-bold text-primary-600" x-text="formatRupiah(grandTotal)"></span>
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
                        placeholder="Syarat penawaran">{{ old('terms', 'Harga berlaku selama masa penawaran. Pembayaran dilakukan setelah quotation disetujui.') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Visibility Options -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-sm font-medium text-gray-700 mb-4">Pengaturan Dokumen</h3>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="include_signature" value="1"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ old('include_signature', true) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Tampilkan Tanda Tangan</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="include_stamp" value="1"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ old('include_stamp', true) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Tampilkan Cap</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="include_qr" value="1"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ old('include_qr', true) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Tampilkan QR Code</span>
                </label>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('quotations.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan
                Quotation</button>
        </div>
    </form>

    @php
        $defaultItems = [
            [
                'product_id' => '',
                'description' => '',
                'quantity' => 1,
                'unit' => 'pcs',
                'unit_price' => 0,
                'tax_percent' => 11
            ]
        ];
        // CRITICAL: array_values() prevents json_encode from casting to Object if indices are non-sequential
        $initialItems = old('items') ? array_values(old('items')) : $defaultItems;
    @endphp

    @push('scripts')
        <script>
            function quotationForm() {
                return {
                    products: @json($products),
                    items: @json($initialItems),
                    subtotal: 0,
                    taxTotal: 0,
                    discountType: {{ old('discount_type', 0) }},
                    discountValue: {{ old('discount_value', 0) }},
                    discountAmount: 0,
                    grandTotal: 0,

                    init() {
                        this.calculateTotal();
                    },

                    addItem() {
                        this.items.push({ product_id: '', description: '', quantity: 1, unit: 'pcs', unit_price: 0, tax_percent: 11 });
                    },

                    removeItem(index) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    },

                    selectProduct(item) {
                        const product = this.products.find(p => p.id == item.product_id);
                        if (product) {
                            item.description = product.name;
                            item.unit = product.unit || 'pcs';
                            item.unit_price = parseFloat(product.selling_price) || 0;
                            // Optionally set tax if you have it in product
                            // item.tax_percent = product.tax_rate || 0;
                            this.calculateTotal();
                        }
                    },

                    getItemSubtotal(item) {
                        const base = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                        const tax = base * ((parseFloat(item.tax_percent) || 0) / 100);
                        return base + tax;
                    },

                    calculateTotal() {
                        this.subtotal = this.items.reduce((sum, item) => {
                            return sum + (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                        }, 0);

                        this.taxTotal = this.items.reduce((sum, item) => {
                            const base = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                            return sum + base * ((parseFloat(item.tax_percent) || 0) / 100);
                        }, 0);

                        if (this.discountType == 1) {
                            this.discountAmount = this.subtotal * ((parseFloat(this.discountValue) || 0) / 100);
                        } else {
                            this.discountAmount = parseFloat(this.discountValue) || 0;
                        }

                        this.grandTotal = this.subtotal + this.taxTotal - this.discountAmount;
                    },

                    formatRupiah(num) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(num || 0);
                    }
                }
            }
        </script>
    @endpush
@endsection
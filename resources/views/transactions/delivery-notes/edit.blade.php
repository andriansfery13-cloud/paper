@extends('layouts.app')

@section('title', 'Edit Surat Jalan')
@section('header', 'Edit Surat Jalan')

@section('content')
    <div class="max-w-4xl mx-auto" x-data="deliveryNoteForm()">
        <form action="{{ route('delivery-notes.update', $deliveryNote) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $deliveryNote->delivery_number }}</h2>
                        <p class="text-gray-500">Invoice: {{ $deliveryNote->invoice->invoice_number }}</p>
                    </div>
                    <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                        {{ $deliveryNote->status_label }}
                    </span>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengiriman</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengiriman <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="delivery_date"
                            value="{{ old('delivery_date', $deliveryNote->delivery_date->format('Y-m-d')) }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="recipient_name"
                            value="{{ old('recipient_name', $deliveryNote->recipient_name) }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman</label>
                        <textarea name="recipient_address" rows="2"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('recipient_address', $deliveryNote->recipient_address) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon Penerima</label>
                        <input type="text" name="recipient_phone"
                            value="{{ old('recipient_phone', $deliveryNote->recipient_phone) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supir</label>
                        <input type="text" name="driver_name" value="{{ old('driver_name', $deliveryNote->driver_name) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Kendaraan</label>
                        <input type="text" name="vehicle_number"
                            value="{{ old('vehicle_number', $deliveryNote->vehicle_number) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <input type="text" name="notes" value="{{ old('notes', $deliveryNote->notes) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Barang</h3>
                    <button type="button" @click="addItem()"
                        class="px-3 py-1.5 text-sm bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100">
                        + Tambah Barang
                    </button>
                </div>

                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Jumlah</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Satuan</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                            <th class="w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b">
                                <td class="px-3 py-2">
                                    <input type="hidden" :name="'items[' + index + '][id]'" x-model="item.id">
                                    <input type="text" :name="'items[' + index + '][description]'"
                                        x-model="item.description" required
                                        class="w-full px-3 py-1.5 border rounded text-sm focus:ring-primary-500 focus:border-primary-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity"
                                        required min="0.01" step="0.01"
                                        class="w-full px-3 py-1.5 border rounded text-sm text-center focus:ring-primary-500 focus:border-primary-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'items[' + index + '][unit]'" x-model="item.unit" required
                                        class="w-full px-3 py-1.5 border rounded text-sm text-center focus:ring-primary-500 focus:border-primary-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'items[' + index + '][notes]'" x-model="item.notes"
                                        class="w-full px-3 py-1.5 border rounded text-sm focus:ring-primary-500 focus:border-primary-500">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="text-red-500 hover:text-red-700">
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
            </div>

            <!-- Document Settings -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Dokumen</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="include_signature" value="1"
                                {{ old('include_signature', $deliveryNote->include_signature) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                            <span class="ml-2 text-gray-700">Tampilkan Tanda Tangan</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1 ml-6">Tanda tangan digital akan ditampilkan di dokumen</p>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="include_stamp" value="1"
                                {{ old('include_stamp', $deliveryNote->include_stamp) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                            <span class="ml-2 text-gray-700">Tampilkan Cap</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1 ml-6">Cap perusahaan akan ditampilkan di dokumen</p>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="include_qr" value="1"
                                {{ old('include_qr', $deliveryNote->include_qr) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                            <span class="ml-2 text-gray-700">Tampilkan QR Code</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1 ml-6">QR Code untuk verifikasi dokumen akan ditampilkan</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('delivery-notes.show', $deliveryNote) }}"
                    class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        function deliveryNoteForm() {
            return {
                items: [
                    @foreach($deliveryNote->items as $item)
                        {
                                id: {{ $item->id }},
                                description: @json($item->description),
                                quantity: {{ $item->quantity }},
                                unit: @json($item->unit),
                                notes: @json($item->notes ?? '')
                            },
                    @endforeach
            ],
                addItem() {
                    this.items.push({ id: null, description: '', quantity: 1, unit: 'pcs', notes: '' });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }
    </script>
@endsection
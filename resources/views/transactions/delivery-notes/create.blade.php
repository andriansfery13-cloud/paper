@extends('layouts.app')

@section('title', 'Buat Surat Jalan')
@section('header', 'Buat Surat Jalan')

@section('content')
    <div class="max-w-4xl mx-auto" x-data="deliveryNoteForm()">
        <form action="{{ route('delivery-notes.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

            <!-- Invoice Info -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Invoice</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">No. Invoice</p>
                        <p class="font-medium text-primary-600">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Client</p>
                        <p class="font-medium">{{ $invoice->client->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="font-medium">{{ $invoice->formatted_total }}</p>
                    </div>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengiriman</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat Jalan <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="delivery_number" value="{{ old('delivery_number', $nextNumber ?? '') }}"
                            required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        @error('delivery_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengiriman <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="recipient_name"
                            value="{{ old('recipient_name', $invoice->client->contact_person ?? $invoice->client->name) }}"
                            required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman</label>
                        <textarea name="recipient_address" rows="2"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('recipient_address', $invoice->client->address) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon Penerima</label>
                        <input type="text" name="recipient_phone"
                            value="{{ old('recipient_phone', $invoice->client->phone) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supir</label>
                        <input type="text" name="driver_name" value="{{ old('driver_name') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Kendaraan</label>
                        <input type="text" name="vehicle_number" value="{{ old('vehicle_number') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="B 1234 ABC">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <input type="text" name="notes" value="{{ old('notes') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Catatan pengiriman">
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
                                        class="w-full px-3 py-1.5 border rounded text-sm focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="Opsional">
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

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('invoices.show', $invoice) }}"
                    class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Buat Surat Jalan
                </button>
            </div>
        </form>
    </div>

    <script>
        function deliveryNoteForm() {
            return {
                items: [
                    @foreach($invoice->items as $item)
                                {
                            description: @json($item->description),
                            quantity: {{ $item->quantity }},
                            unit: @json($item->unit),
                            notes: ''
                        },
                    @endforeach
                ],
                addItem() {
                    this.items.push({ description: '', quantity: 1, unit: 'pcs', notes: '' });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }
    </script>
@endsection
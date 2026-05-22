@extends('layouts.app')

@section('title', 'Pengaturan Perusahaan')
@section('header', 'Pengaturan Perusahaan')

@section('content')
    <div class="max-w-4xl">
        <!-- Settings Navigation -->
        <div class="mb-6 border-b">
            <nav class="flex gap-4">
                <a href="{{ route('settings.company') }}"
                    class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('settings.company') ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Perusahaan
                </a>
                <a href="{{ route('settings.invoice') }}"
                    class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('settings.invoice') ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Invoice & Dokumen
                </a>
                <a href="{{ route('settings.email') }}"
                    class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('settings.email') ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Email
                </a>
            </nav>
        </div>

        <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Company Info -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Perusahaan</h3>

                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name', $tenant->company_name) }}"
                            required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        @error('company_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <input type="email" name="email" value="{{ old('email', $tenant->email) }}" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            @if($tenant->email && !$tenant->email_verified_at)
                                <button type="button" @click="$dispatch('open-otp-modal', { type: 'email' })"
                                    class="px-3 py-2 text-sm bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 whitespace-nowrap">
                                    Verifikasi
                                </button>
                            @elseif($tenant->email_verified_at)
                                <span
                                    class="flex items-center gap-1 px-3 py-2 text-sm bg-green-100 text-green-700 rounded-lg whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Terverifikasi
                                </span>
                            @endif
                        </div>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            @if($tenant->phone && !$tenant->phone_verified_at)
                                <button type="button" @click="$dispatch('open-otp-modal', { type: 'phone' })"
                                    class="px-3 py-2 text-sm bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 whitespace-nowrap">
                                    Verifikasi
                                </button>
                            @elseif($tenant->phone_verified_at)
                                <span
                                    class="flex items-center gap-1 px-3 py-2 text-sm bg-green-100 text-green-700 rounded-lg whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Terverifikasi
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" name="website" value="{{ old('website', $tenant->website) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="https://example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                        <input type="text" name="npwp" value="{{ old('npwp', $tenant->npwp) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="00.000.000.0-000.000">
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Alamat</h3>

                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="address" rows="2"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('address', $tenant->address) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" name="city" value="{{ old('city', $tenant->city) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province', $tenant->province) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $tenant->postal_code) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
            </div>

            <!-- Logo & Images -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Logo & Gambar</h3>

                <div class="grid grid-cols-3 gap-6">
                    <!-- Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo Perusahaan</label>
                        @if($tenant->logo)
                            <div class="mb-2">
                                <img src="{{ Storage::url($tenant->logo) }}" alt="Logo" class="h-16 w-auto rounded border">
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF. Max 2MB</p>
                        @error('logo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stamp -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stempel</label>
                        @if($tenant->stamp_image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($tenant->stamp_image) }}" alt="Stamp"
                                    class="h-16 w-auto rounded border">
                            </div>
                        @endif
                        <input type="file" name="stamp_image" accept="image/png"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="text-xs text-gray-500 mt-1">PNG saja. Max 1MB</p>
                        @error('stamp_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Signature -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan</label>
                        @if($tenant->signature_image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($tenant->signature_image) }}" alt="Signature"
                                    class="h-16 w-auto rounded border">
                            </div>
                        @endif
                        <input type="file" name="signature_image" accept="image/png"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="text-xs text-gray-500 mt-1">PNG saja. Max 1MB</p>
                        @error('signature_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- OTP Modal -->
    <div x-data="{ 
        show: false, 
        type: '', 
        otp: '', 
        loading: false, 
        message: '',
        success: false,
        init() {
            window.addEventListener('open-otp-modal', (e) => {
                this.type = e.detail.type;
                this.show = true;
                this.message = '';
                this.success = false;
                this.otp = '';
                this.sendOtp();
            });
        },
        async sendOtp() {
            this.loading = true;
            this.message = 'Mengirim OTP...';
            
            try {
                const response = await fetch('{{ route('settings.company.send-otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type: this.type })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.message = 'OTP telah dikirim ke ' + (this.type === 'email' ? 'email' : 'WhatsApp') + ' Anda.';
                } else {
                    this.message = 'Gagal mengirim OTP: ' + data.message;
                }
            } catch (error) {
                this.message = 'Terjadi kesalahan sistem.';
            } finally {
                this.loading = false;
            }
        },
        async verify() {
             this.loading = true;
             
             try {
                const response = await fetch('{{ route('settings.company.verify-otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type: this.type, otp: this.otp })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.success = true;
                    this.message = data.message;
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.message = data.message;
                }
            } catch (error) {
                this.message = 'Terjadi kesalahan saat verifikasi.';
            } finally {
                this.loading = false;
            }
        }
    }" 
    x-show="show" style="display: none;" 
    class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
             <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>
             
             <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="'Verifikasi ' + (type === 'email' ? 'Email' : 'No HP')"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="message"></p>
                                
                                <div class="mt-4" x-show="!success">
                                    <input type="text" x-model="otp" placeholder="Masukkan 6 digit Kode OTP" class="w-full px-3 py-2 border rounded shadow-sm focus:ring-primary-500 focus:border-primary-500 text-center text-lg tracking-widest">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse" x-show="!success">
                    <button type="button" @click="verify()" :disabled="loading || otp.length < 6" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        <span x-show="!loading">Verifikasi</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                    <button type="button" @click="sendOtp()" :disabled="loading" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Kirim Ulang OTP
                    </button>
                    <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
             </div>
        </div>
    </div>
@endsection
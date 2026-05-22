@extends('layouts.superadmin')

@section('title', 'Edit Tenant')
@section('header', 'Edit Tenant')

@section('content')
    <div class="mb-6">
        <a href="{{ route('superadmin.tenants.show', $tenant) }}"
            class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Kembali ke Detail Tenant
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">{{ $tenant->company_name ?? 'Unnamed Tenant' }}</h2>

        <form action="{{ route('superadmin.tenants.update', $tenant) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500">
                        <option value="active" {{ $tenant->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ $tenant->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="cancelled" {{ $tenant->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Paket Langganan</label>
                    <select name="current_plan_id" id="plan-select"
                        class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Pilih Paket --</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" 
                                data-trial="{{ $plan->trial_days }}"
                                {{ $tenant->current_plan_id == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}/Bulan
                            </option>
                        @endforeach
                    </select>
                    @error('current_plan_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Langganan Berakhir</label>
                    <input type="date" name="subscription_ends_at" id="subscription-date"
                        value="{{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('Y-m-d') : '' }}"
                        class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500">
                    @error('subscription_ends_at')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('superadmin.tenants.show', $tenant) }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('plan-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const trialDays = parseInt(selectedOption.getAttribute('data-trial')) || 0;
            const dateInput = document.getElementById('subscription-date');
            
            if (this.value) {
                const today = new Date();
                let endDate = new Date(today);
                
                if (trialDays > 0) {
                    // If plan has trial days, add them to today
                    endDate.setDate(today.getDate() + trialDays);
                } else {
                    // Default to 30 days (1 month) if no trial logic specified, 
                    // assuming admin wants to start a standard month subscription
                    endDate.setDate(today.getDate() + 30);
                }
                
                // Format to YYYY-MM-DD
                const yyyy = endDate.getFullYear();
                const mm = String(endDate.getMonth() + 1).padStart(2, '0');
                const dd = String(endDate.getDate()).padStart(2, '0');
                
                dateInput.value = `${yyyy}-${mm}-${dd}`;
            }
        });
    </script>
@endsection
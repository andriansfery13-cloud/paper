<!-- Quota Limit Popup -->
<div x-data="quotaPopup()" x-show="showPopup" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="closePopup()"></div>

    <!-- Modal -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">

            <!-- Close Button -->
            <button @click="closePopup()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <!-- Icon -->
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Content -->
            <div class="text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Kuota Habis!</h3>
                <p class="text-gray-600 mb-4" x-text="message"></p>

                <!-- Current Usage -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <p class="text-sm font-semibold text-gray-800 mb-3">📊 Penggunaan Saat Ini:</p>
                    <div class="space-y-3">
                        <!-- Invoice -->
                        <template x-if="usage.invoices">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Invoice</span>
                                    <span class="font-medium"
                                        :class="usage.invoices.used >= usage.invoices.max && usage.invoices.max != -1 ? 'text-red-600' : 'text-green-600'">
                                        <span x-text="usage.invoices.used"></span> / <span
                                            x-text="usage.invoices.max == -1 ? '∞' : usage.invoices.max"></span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full"
                                        :class="usage.invoices.used >= usage.invoices.max && usage.invoices.max != -1 ? 'bg-red-500' : 'bg-green-500'"
                                        :style="'width: ' + (usage.invoices.max == -1 ? 30 : Math.min(100, (usage.invoices.used / usage.invoices.max) * 100)) + '%'">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Client -->
                        <template x-if="usage.clients">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Client</span>
                                    <span class="font-medium"
                                        :class="usage.clients.used >= usage.clients.max && usage.clients.max != -1 ? 'text-red-600' : 'text-green-600'">
                                        <span x-text="usage.clients.used"></span> / <span
                                            x-text="usage.clients.max == -1 ? '∞' : usage.clients.max"></span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full"
                                        :class="usage.clients.used >= usage.clients.max && usage.clients.max != -1 ? 'bg-red-500' : 'bg-green-500'"
                                        :style="'width: ' + (usage.clients.max == -1 ? 30 : Math.min(100, (usage.clients.used / usage.clients.max) * 100)) + '%'">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Product -->
                        <template x-if="usage.products">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Produk</span>
                                    <span class="font-medium"
                                        :class="usage.products.used >= usage.products.max && usage.products.max != -1 ? 'text-red-600' : 'text-green-600'">
                                        <span x-text="usage.products.used"></span> / <span
                                            x-text="usage.products.max == -1 ? '∞' : usage.products.max"></span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full"
                                        :class="usage.products.used >= usage.products.max && usage.products.max != -1 ? 'bg-red-500' : 'bg-green-500'"
                                        :style="'width: ' + (usage.products.max == -1 ? 30 : Math.min(100, (usage.products.used / usage.products.max) * 100)) + '%'">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Quotation -->
                        <template x-if="usage.quotations">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Penawaran</span>
                                    <span class="font-medium"
                                        :class="usage.quotations.used >= usage.quotations.max && usage.quotations.max != -1 ? 'text-red-600' : 'text-green-600'">
                                        <span x-text="usage.quotations.used"></span> / <span
                                            x-text="usage.quotations.max == -1 ? '∞' : usage.quotations.max"></span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full"
                                        :class="usage.quotations.used >= usage.quotations.max && usage.quotations.max != -1 ? 'bg-red-500' : 'bg-green-500'"
                                        :style="'width: ' + (usage.quotations.max == -1 ? 30 : Math.min(100, (usage.quotations.used / usage.quotations.max) * 100)) + '%'">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- User -->
                        <template x-if="usage.users">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">User</span>
                                    <span class="font-medium"
                                        :class="usage.users.used >= usage.users.max && usage.users.max != -1 ? 'text-red-600' : 'text-green-600'">
                                        <span x-text="usage.users.used"></span> / <span
                                            x-text="usage.users.max == -1 ? '∞' : usage.users.max"></span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full"
                                        :class="usage.users.used >= usage.users.max && usage.users.max != -1 ? 'bg-red-500' : 'bg-green-500'"
                                        :style="'width: ' + (usage.users.max == -1 ? 30 : Math.min(100, (usage.users.used / usage.users.max) * 100)) + '%'">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Plan Info -->
                <p class="text-sm text-gray-500 mb-4">
                    Upgrade paket Anda untuk mendapatkan kuota lebih banyak dan fitur premium lainnya.
                </p>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="{{ route('subscription.pricing') }}"
                        class="block w-full px-6 py-3 bg-primary-600 text-white rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                        🚀 Upgrade Paket Sekarang
                    </a>
                    <button @click="closePopup()"
                        class="block w-full px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                        Nanti Saja
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function quotaPopup() {
        return {
            showPopup: false,
            message: '',
            quotaType: 'all',
            usage: {},

            init() {
                window.addEventListener('quota-exceeded', (e) => {
                    this.showQuotaPopup(e.detail);
                });
            },

            showQuotaPopup(detail) {
                this.quotaType = detail.type || 'all';
                this.message = detail.message || 'Anda telah mencapai batas kuota untuk paket Anda saat ini.';
                this.usage = detail.usage || {};
                this.showPopup = true;
            },

            closePopup() {
                this.showPopup = false;
            }
        }
    }

    function showQuotaExceeded(type, message, usage) {
        window.dispatchEvent(new CustomEvent('quota-exceeded', {
            detail: { type, message, usage }
        }));
    }
</script>
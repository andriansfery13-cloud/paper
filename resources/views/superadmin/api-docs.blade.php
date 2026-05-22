@extends('layouts.superadmin')

@section('title', 'API Documentation')
@section('header', 'API Documentation')

@section('content')
    <div class="space-y-8">
        <!-- Introduction -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">SaaS Finance API</h2>
            <p class="text-gray-600 mb-4">
                API ini memungkinkan Anda untuk mengintegrasikan sistem invoicing dengan aplikasi lain.
                Semua request harus menggunakan Bearer Token yang didapat dari endpoint login.
            </p>
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">Base URL</h4>
                <code class="text-sm text-primary-600">{{ url('/api/v1') }}</code>
            </div>
        </div>

        <!-- Authentication -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span
                    class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3 text-sm font-bold">1</span>
                Authentication
            </h3>

            <!-- Login -->
            <div class="border border-gray-200 rounded-lg mb-4">
                <div class="flex items-center p-4 bg-gray-50 border-b">
                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded mr-3">POST</span>
                    <code class="text-sm">/api/v1/login</code>
                </div>
                <div class="p-4">
                    <p class="text-gray-600 mb-4">Login untuk mendapatkan access token</p>
                    <h5 class="font-semibold text-gray-900 mb-2">Request Body</h5>
                    <pre class="bg-gray-50 rounded-lg p-4 text-sm overflow-x-auto"><code>{
            "email": "user@example.com",
            "password": "your_password"
        }</code></pre>
                    <h5 class="font-semibold text-gray-900 mt-4 mb-2">Response</h5>
                    <pre class="bg-gray-50 rounded-lg p-4 text-sm overflow-x-auto"><code>{
            "success": true,
            "token": "1|abcdef123456...",
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "user@example.com"
            }
        }</code></pre>
                </div>
            </div>

            <!-- Using Token -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h5 class="font-semibold text-blue-900 mb-2">Menggunakan Token</h5>
                <p class="text-sm text-blue-800 mb-2">Setelah login, gunakan token pada header Authorization untuk semua
                    request:</p>
                <code class="text-sm text-blue-700">Authorization: Bearer YOUR_TOKEN_HERE</code>
            </div>
        </div>

        <!-- Clients -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span
                    class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 text-sm font-bold">2</span>
                Clients
            </h3>

            <div class="space-y-4">
                <!-- List Clients -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center p-4 bg-gray-50 border-b">
                        <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded mr-3">GET</span>
                        <code class="text-sm">/api/v1/clients</code>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600">Mendapatkan daftar semua client</p>
                        <div class="mt-2">
                            <span class="text-xs font-medium text-gray-500">Query Parameters:</span>
                            <span class="text-sm text-gray-600 ml-2">page, per_page, search</span>
                        </div>
                    </div>
                </div>

                <!-- Create Client -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center p-4 bg-gray-50 border-b">
                        <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded mr-3">POST</span>
                        <code class="text-sm">/api/v1/clients</code>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600 mb-2">Membuat client baru</p>
                        <pre class="bg-gray-50 rounded-lg p-4 text-sm overflow-x-auto"><code>{
            "name": "Client Name",
            "email": "client@example.com",
            "phone": "081234567890",
            "company": "Company Name",
            "address": "123 Main St"
        }</code></pre>
                    </div>
                </div>

                <!-- Get Client -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center p-4 bg-gray-50 border-b">
                        <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded mr-3">GET</span>
                        <code class="text-sm">/api/v1/clients/{id}</code>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600">Mendapatkan detail client berdasarkan ID</p>
                    </div>
                </div>

                <!-- Update Client -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center p-4 bg-gray-50 border-b">
                        <span class="px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded mr-3">PUT</span>
                        <code class="text-sm">/api/v1/clients/{id}</code>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600">Update data client</p>
                    </div>
                </div>

                <!-- Delete Client -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center p-4 bg-gray-50 border-b">
                        <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded mr-3">DELETE</span>
                        <code class="text-sm">/api/v1/clients/{id}</code>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600">Menghapus client</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span
                    class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3 text-sm font-bold">3</span>
                Invoices
            </h3>

            <div class="space-y-4">
                <!-- List Invoices -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center p-4 bg-gray-50 border-b">
                        <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded mr-3">GET</span>
                        <code class="text-sm">/api/v1/invoices</code>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600">Mendapatkan daftar semua invoice</p>
                        <div class="mt-2">
                            <span class="text-xs font-medium text-gray-500">Query Parameters:</span>
                            <span class="text-sm text-gray-600 ml-2">page, per_page, status, client_id</span>
                        </div>
                    </div>
                </div>

                <!-- Create Invoice -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center p-4 bg-gray-50 border-b">
                        <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded mr-3">POST</span>
                        <code class="text-sm">/api/v1/invoices</code>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600 mb-2">Membuat invoice baru</p>
                        <pre class="bg-gray-50 rounded-lg p-4 text-sm overflow-x-auto"><code>{
            "client_id": 1,
            "due_date": "2026-02-28",
            "subject": "Website Development",
            "items": [
                {
                    "description": "Web Design",
                    "quantity": 1,
                    "unit_price": 5000000
                },
                {
                    "description": "Development",
                    "quantity": 10,
                    "unit_price": 500000
                }
            ]
        }</code></pre>
                    </div>
                </div>

                <!-- Send Invoice -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center p-4 bg-gray-50 border-b">
                        <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded mr-3">POST</span>
                        <code class="text-sm">/api/v1/invoices/{id}/send</code>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600">Mengirim invoice ke client</p>
                    </div>
                </div>

                <!-- Get Invoice -->
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center p-4 bg-gray-50 border-b">
                        <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded mr-3">GET</span>
                        <code class="text-sm">/api/v1/invoices/{id}</code>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600">Mendapatkan detail invoice beserta items dan payments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rate Limiting -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span
                    class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mr-3 text-sm font-bold">!</span>
                Rate Limiting
            </h3>
            <div class="space-y-3 text-gray-600">
                <p>API ini menggunakan rate limiting untuk melindungi server:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Login:</strong> 5 request per menit</li>
                    <li><strong>API Endpoints:</strong> 60 request per menit per user</li>
                </ul>
                <p class="text-sm">Jika melebihi limit, Anda akan menerima response 429 Too Many Requests.</p>
            </div>
        </div>

        <!-- Error Responses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span
                    class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center mr-3 text-sm font-bold">!</span>
                Error Responses
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="font-semibold text-gray-900 mb-2">401 Unauthorized</h5>
                    <p class="text-sm text-gray-600">Token tidak valid atau sudah expired</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="font-semibold text-gray-900 mb-2">404 Not Found</h5>
                    <p class="text-sm text-gray-600">Resource tidak ditemukan</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="font-semibold text-gray-900 mb-2">422 Validation Error</h5>
                    <p class="text-sm text-gray-600">Data yang dikirim tidak valid</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="font-semibold text-gray-900 mb-2">429 Too Many Requests</h5>
                    <p class="text-sm text-gray-600">Rate limit tercapai</p>
                </div>
            </div>
        </div>
    </div>
@endsection
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    @php
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
    @endphp
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .container {
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }

        .company-info h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #666;
            font-size: 11px;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-info h2 {
            font-size: 28px;
            color: #1e40af;
            text-transform: uppercase;
        }

        .invoice-info .invoice-number {
            font-size: 14px;
            color: #333;
            margin-top: 5px;
        }

        .meta-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .meta-box {
            width: 48%;
        }

        .meta-box h3 {
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .meta-box p {
            color: #333;
        }

        .meta-box .name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .dates-box {
            text-align: right;
        }

        .dates-box table {
            width: 100%;
        }

        .dates-box td {
            padding: 3px 0;
        }

        .dates-box td:first-child {
            color: #666;
        }

        .dates-box td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #3b82f6;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table th:last-child,
        .items-table th:nth-child(3),
        .items-table th:nth-child(4),
        .items-table th:nth-child(5) {
            text-align: right;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .items-table td:last-child,
        .items-table td:nth-child(3),
        .items-table td:nth-child(4),
        .items-table td:nth-child(5) {
            text-align: right;
        }

        .items-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
        }

        .totals-table {
            width: 300px;
        }

        .totals-table tr td {
            padding: 8px 0;
        }

        .totals-table tr td:first-child {
            color: #666;
        }

        .totals-table tr td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .totals-table .total-row {
            border-top: 2px solid #333;
            font-size: 16px;
        }

        .totals-table .total-row td {
            font-weight: bold;
            color: #1e40af;
            padding-top: 12px;
        }

        .total-due {
            background-color: #fef3c7;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
            border-radius: 8px;
        }

        .total-due h3 {
            font-size: 12px;
            color: #92400e;
            margin-bottom: 5px;
        }

        .total-due .amount {
            font-size: 24px;
            font-weight: bold;
            color: #92400e;
        }

        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
        }

        .notes-section h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
        }

        .notes-section p {
            color: #333;
            font-size: 11px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 10px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .qr-section {
            text-align: center;
            margin-top: 20px;
        }

        .qr-section p {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-unpaid {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <table style="width: 100%; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 20px;">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <!-- Logo Section -->
                    @if($invoice->tenant->logo && file_exists(public_path('storage/' . $invoice->tenant->logo)))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $invoice->tenant->logo))) }}"
                            style="height: 60px; margin-bottom: 10px; max-width: 200px; object-fit: contain;">
                    @else
                        <h1 style="font-size: 24px; color: #1e40af; margin-bottom: 5px;">
                            {{ $invoice->tenant->company_name }}
                        </h1>
                    @endif

                    @if($invoice->tenant->logo)
                        <p style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">
                            {{ $invoice->tenant->company_name }}
                        </p>
                    @endif

                    <p style="color: #666; font-size: 11px;">{{ $invoice->tenant->address }}</p>
                    <p style="color: #666; font-size: 11px;">{{ $invoice->tenant->city }},
                        {{ $invoice->tenant->province }} {{ $invoice->tenant->postal_code }}
                    </p>
                    <p style="color: #666; font-size: 11px;">Tel: {{ $invoice->tenant->phone }} | Email:
                        {{ $invoice->tenant->email }}
                    </p>
                    @if($invoice->tenant->npwp)
                        <p style="color: #666; font-size: 11px;">NPWP: {{ $invoice->tenant->npwp }}</p>
                    @endif
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <h2 style="font-size: 28px; color: #1e40af; text-transform: uppercase;">INVOICE</h2>
                    <p style="font-size: 14px; color: #333; margin-top: 5px;">{{ $invoice->invoice_number }}</p>
                    <span class="status-badge {{ $invoice->status == 'paid' ? 'status-paid' : 'status-unpaid' }}">
                        {{ $invoice->status == 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- Client & Dates -->
        <table style="width: 100%; margin-bottom: 30px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <p style="font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 5px;">Ditagihkan
                        Kepada:</p>
                    <p style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">{{ $invoice->client->name }}</p>
                    <p style="color: #666; font-size: 11px;">{{ $invoice->client->address }}</p>
                    <p style="color: #666; font-size: 11px;">{{ $invoice->client->city }},
                        {{ $invoice->client->province }}
                    </p>
                    @if($invoice->client->npwp)
                        <p style="color: #666; font-size: 11px;">NPWP: {{ $invoice->client->npwp }}</p>
                    @endif
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="color: #666; padding: 3px 0;">Tanggal Invoice:</td>
                            <td style="text-align: right; font-weight: 500;">
                                {{ $invoice->invoice_date->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #666; padding: 3px 0;">Jatuh Tempo:</td>
                            <td
                                style="text-align: right; font-weight: 500; color: {{ $invoice->due_date < now() && $invoice->status != 'paid' ? '#dc2626' : '#333' }};">
                                {{ $invoice->due_date->format('d M Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if($invoice->subject)
            <p style="margin-bottom: 20px;"><strong>Perihal:</strong> {{ $invoice->subject }}</p>
        @endif

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 40%;">Deskripsi</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 15%;">Harga</th>
                    <th style="width: 10%;">Pajak</th>
                    <th style="width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td style="text-align: right;">{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($item->tax_percent, 0) }}%</td>
                        <td style="text-align: right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="color: #666; padding: 8px 0;">Subtotal</td>
                            <td style="text-align: right; font-weight: 500;">Rp
                                {{ number_format($invoice->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                            <tr>
                                <td style="color: #666; padding: 8px 0;">Diskon</td>
                                <td style="text-align: right; font-weight: 500; color: #dc2626;">- Rp
                                    {{ number_format($invoice->discount_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td style="color: #666; padding: 8px 0;">PPN</td>
                            <td style="text-align: right; font-weight: 500;">Rp
                                {{ number_format($invoice->tax_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @if($invoice->shipping_amount > 0)
                            <tr>
                                <td style="color: #666; padding: 8px 0;">Ongkos Kirim</td>
                                <td style="text-align: right; font-weight: 500;">Rp
                                    {{ number_format($invoice->shipping_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        <tr style="border-top: 2px solid #333;">
                            <td style="font-weight: bold; color: #1e40af; padding-top: 12px; font-size: 14px;">Total
                            </td>
                            <td
                                style="text-align: right; font-weight: bold; color: #1e40af; padding-top: 12px; font-size: 14px;">
                                Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        </tr>
                        @if($invoice->amount_paid > 0)
                            <tr>
                                <td style="color: #666; padding: 8px 0;">Sudah Dibayar</td>
                                <td style="text-align: right; font-weight: 500; color: #059669;">Rp
                                    {{ number_format($invoice->amount_paid, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; color: #dc2626; padding: 8px 0;">Sisa Tagihan</td>
                                <td style="text-align: right; font-weight: bold; color: #dc2626;">Rp
                                    {{ number_format($invoice->amount_due, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <!-- Notes & Terms -->
        @if($invoice->notes || $invoice->terms)
            <div style="margin-top: 30px; padding: 15px; background-color: #f3f4f6; border-radius: 8px;">
                @if($invoice->notes)
                    <div style="margin-bottom: 15px;">
                        <h3 style="font-size: 11px; text-transform: uppercase; color: #666; margin-bottom: 8px;">Catatan</h3>
                        <p style="color: #333; font-size: 11px;">{{ $invoice->notes }}</p>
                    </div>
                @endif
                @if($invoice->terms)
                    <div>
                        <h3 style="font-size: 11px; text-transform: uppercase; color: #666; margin-bottom: 8px;">Syarat &
                            Ketentuan</h3>
                        <p style="color: #333; font-size: 11px;">{{ $invoice->terms }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Signature & QR Section -->
        <table style="width: 100%; margin-top: 50px;">
            <tr>
                <td style="width: 60%; vertical-align: bottom;">
                    @if($invoice->include_qr)
                        <div style="color: #666; font-size: 10px;">
                            <p style="margin-bottom: 5px;">Scan barcode untuk verifikasi keaslian dokumen:</p>
                            <img src="data:image/svg+xml;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(80)->generate(route('verify.invoice', $invoice->verification_code ?? $invoice->invoice_number))) !!}"
                                alt="QR Verification">
                            <p style="margin-top: 5px;">Kode: <strong>{{ $invoice->invoice_number }}</strong></p>
                        </div>
                    @endif
                </td>
                <td style="width: 40%; text-align: center; vertical-align: top;">
                    <p style="margin-bottom: 5px; font-size: 11px;">{{ $invoice->tenant->city }},
                        {{ $invoice->invoice_date->format('d M Y') }}
                    </p>
                    <p style="font-weight: bold; margin-bottom: 20px; font-size: 11px;">
                        {{ $invoice->tenant->company_name }}
                    </p>

                    <div style="position: relative; height: 90px; width: 100%; margin-bottom: 10px;">
                        @if($invoice->include_stamp && $invoice->tenant->stamp_image && file_exists(public_path('storage/' . $invoice->tenant->stamp_image)))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $invoice->tenant->stamp_image))) }}"
                                style="height: 80px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 1; opacity: 0.8;">
                        @endif

                        @if($invoice->include_signature && $invoice->tenant->signature_image && file_exists(public_path('storage/' . $invoice->tenant->signature_image)))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $invoice->tenant->signature_image))) }}"
                                style="height: 80px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 2;">
                        @endif
                    </div>

                    @if($invoice->include_signature)
                        <p style="font-size: 11px; text-decoration: underline; font-weight: bold;">
                            {{ $invoice->creator->name ?? 'Admin' }}
                        </p>
                        <p style="font-size: 10px;">Authorized Signature</p>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div
            style="margin-top: 30px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #eee; padding-top: 20px;">
            <p>Dokumen ini dibuat secara otomatis oleh sistem Paperly.</p>
            <p style="margin-top: 5px;">Kode Verifikasi: {{ $invoice->uuid }}</p>
            <p style="margin-top: 5px;">Link Verifikasi:
                {{ route('verify.invoice', $invoice->verification_code ?? $invoice->invoice_number) }}
            </p>
        </div>
    </div>
</body>

</html>
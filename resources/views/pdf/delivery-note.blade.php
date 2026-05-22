<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Jalan {{ $deliveryNote->delivery_number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.4;
        }

        .container {
            padding: 10px;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: middle;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 9pt;
            color: #333;
            line-height: 1.5;
        }

        .doc-title {
            font-size: 20pt;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .doc-number {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 5px;
            color: #c00;
        }

        /* Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-left,
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-box {
            background: #f5f5f5;
            padding: 10px 15px;
            margin-right: 10px;
            border-left: 4px solid #333;
        }

        .info-right .info-box {
            margin-right: 0;
            margin-left: 10px;
        }

        .info-title {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }

        .info-content {
            font-size: 10pt;
        }

        .info-content strong {
            font-size: 11pt;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #333;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            text-transform: uppercase;
        }

        .items-table th.center {
            text-align: center;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10pt;
        }

        .items-table td.center {
            text-align: center;
        }

        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Notes */
        .notes-section {
            margin-bottom: 20px;
            padding: 10px;
            background: #fffde7;
            border: 1px solid #ffc107;
        }

        .notes-title {
            font-weight: bold;
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }

        /* Delivery Info */
        .delivery-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        .delivery-info-item {
            display: table-cell;
            width: 25%;
            padding-right: 10px;
        }

        .delivery-info-label {
            color: #666;
        }

        /* Signature Section */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-title {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 60px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin: 0 30px 5px 30px;
        }

        .signature-name {
            font-size: 9pt;
            min-height: 15px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 3px;
            margin-top: 10px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-in_transit {
            background: #cce5ff;
            color: #004085;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        /* Checklist Box */
        .check-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    @php
        $tenant = $deliveryNote->invoice->tenant;
    @endphp

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if($tenant->logo && file_exists(public_path('storage/' . $tenant->logo)))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $tenant->logo))) }}"
                        alt="Logo" style="max-height: 50px; margin-bottom: 5px;">
                @else
                    <div class="company-name">{{ $tenant->company_name }}</div>
                @endif

                @if($tenant->logo)
                    <div class="company-name" style="font-size: 14pt;">{{ $tenant->company_name }}</div>
                @endif

                <div class="company-info">
                    {{ $tenant->address }}<br>
                    {{ $tenant->city }} {{ $tenant->postal_code }}<br>
                    Telp: {{ $tenant->phone }} | Email: {{ $tenant->email }}
                </div>
            </div>
            <div class="header-right">
                <div class="doc-title">Surat Jalan</div>
                <div class="doc-number">{{ $deliveryNote->delivery_number }}</div>
                <div class="status-badge status-{{ $deliveryNote->status }}">
                    {{ $deliveryNote->status_label }}
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <div class="info-title">Penerima</div>
                    <div class="info-content">
                        <strong>{{ $deliveryNote->recipient_name }}</strong><br>
                        {{ $deliveryNote->recipient_address ?? '-' }}<br>
                        @if($deliveryNote->recipient_phone)
                            Telp: {{ $deliveryNote->recipient_phone }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="info-right">
                <div class="info-box">
                    <div class="info-title">Referensi Invoice</div>
                    <div class="info-content">
                        <strong>{{ $deliveryNote->invoice->invoice_number }}</strong><br>
                        Tanggal: {{ $deliveryNote->invoice->invoice_date->format('d/m/Y') }}<br>
                        Client: {{ $deliveryNote->invoice->client->name ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Info -->
        <div class="delivery-info">
            <div class="delivery-info-item">
                <span class="delivery-info-label">Tanggal Kirim:</span><br>
                <strong>{{ $deliveryNote->delivery_date->format('d/m/Y') }}</strong>
            </div>
            @if($deliveryNote->driver_name)
                <div class="delivery-info-item">
                    <span class="delivery-info-label">Nama Supir:</span><br>
                    <strong>{{ $deliveryNote->driver_name }}</strong>
                </div>
            @endif
            @if($deliveryNote->vehicle_number)
                <div class="delivery-info-item">
                    <span class="delivery-info-label">No. Kendaraan:</span><br>
                    <strong>{{ $deliveryNote->vehicle_number }}</strong>
                </div>
            @endif
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40px;" class="center">No</th>
                    <th>Deskripsi Barang</th>
                    <th style="width: 80px;" class="center">Jumlah</th>
                    <th style="width: 60px;" class="center">Satuan</th>
                    <th style="width: 40px;" class="center">✓</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryNote->items as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="center">
                            {{ number_format($item->quantity, $item->quantity == floor($item->quantity) ? 0 : 2) }}
                        </td>
                        <td class="center">{{ $item->unit }}</td>
                        <td class="center"><span class="check-box"></span></td>
                        <td>{{ $item->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Notes -->
        @if($deliveryNote->notes)
            <div class="notes-section">
                <div class="notes-title">Catatan Pengiriman:</div>
                {{ $deliveryNote->notes }}
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Pengirim</div>
                <div class="signature-line" style="position: relative; height: 60px; border-bottom: 1px solid #000;">
                    @if($deliveryNote->include_stamp && $tenant->stamp_image && file_exists(public_path('storage/' . $tenant->stamp_image)))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $tenant->stamp_image))) }}"
                            style="height: 50px; position: absolute; bottom: 0; left: 60%; transform: translateX(-50%); z-index: 1; opacity: 0.8;">
                    @endif

                    @if($deliveryNote->include_signature && $tenant->signature_image && file_exists(public_path('storage/' . $tenant->signature_image)))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $tenant->signature_image))) }}"
                            style="height: 50px; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); z-index: 2;">
                    @endif
                </div>
                <div class="signature-name">
                    @if($deliveryNote->include_signature)
                        {{ $deliveryNote->creator->name ?? '' }}
                    @endif
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Supir / Kurir</div>
                <div class="signature-line" style="height: 60px; border-bottom: 1px solid #000;"></div>
                <div class="signature-name">{{ $deliveryNote->driver_name ?? '________________' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Penerima</div>
                <div class="signature-line" style="height: 60px; border-bottom: 1px solid #000;"></div>
                <div class="signature-name">
                    @if($deliveryNote->status === 'delivered')
                        {{ $deliveryNote->received_by_name }}
                    @else
                        ________________
                    @endif
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            @if($deliveryNote->include_qr)
                <img src="data:image/svg+xml;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->generate(route('verify.delivery-note', $deliveryNote->verification_code))) !!}"
                    alt="QR Verification">
                <p style="font-size: 8pt; color: #666; margin-top: 5px;">Scan untuk verifikasi</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer"
            style="position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8pt; color: #999; border-top: 1px solid #ddd; padding-top: 5px;">
            <p>Dokumen ini dibuat secara otomatis oleh sistem Paperly.</p>
            <p style="margin-top: 5px;">Kode Verifikasi: {{ $deliveryNote->delivery_number }}</p>
            <p style="margin-top: 5px;">Link Verifikasi:
                {{ route('verify.delivery-note', $deliveryNote->verification_code) }}
            </p>
        </div>
    </div>
</body>

</html>
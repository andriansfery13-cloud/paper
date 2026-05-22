<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
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

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #8b5cf6;
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
    </style>
</head>

<body>
    <div class="container">
        <table style="width: 100%; margin-bottom: 30px; border-bottom: 2px solid #8b5cf6; padding-bottom: 20px;">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <!-- Logo Section -->
                    @if($quotation->tenant->logo && file_exists(public_path('storage/' . $quotation->tenant->logo)))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $quotation->tenant->logo))) }}"
                            style="height: 60px; margin-bottom: 10px; max-width: 200px; object-fit: contain;">
                    @else
                        <h1 style="font-size: 24px; color: #7c3aed; margin-bottom: 5px;">
                            {{ $quotation->tenant->company_name }}
                        </h1>
                    @endif

                    @if($quotation->tenant->logo) <!-- If logo exists, render company name smaller -->
                        <p style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">
                            {{ $quotation->tenant->company_name }}
                        </p>
                    @endif

                    <p style="color: #666; font-size: 11px;">{{ $quotation->tenant->address }}</p>
                    <p style="color: #666; font-size: 11px;">{{ $quotation->tenant->city }},
                        {{ $quotation->tenant->province }}
                    </p>
                    <p style="color: #666; font-size: 11px;">Tel: {{ $quotation->tenant->phone }}</p>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <h2 style="font-size: 28px; color: #7c3aed; text-transform: uppercase;">QUOTATION</h2>
                    <p style="font-size: 14px; color: #333; margin-top: 5px;">{{ $quotation->quotation_number }}</p>
                    <span
                        class="status-badge {{ $quotation->status == 'approved' ? 'status-approved' : 'status-pending' }}">
                        {{ strtoupper($quotation->status) }}
                    </span>
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 30px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <p style="font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 5px;">Kepada:</p>
                    <p style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">{{ $quotation->client->name }}
                    </p>
                    <p style="color: #666; font-size: 11px;">{{ $quotation->client->address }}</p>
                    <p style="color: #666; font-size: 11px;">{{ $quotation->client->city }},
                        {{ $quotation->client->province }}
                    </p>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="color: #666; padding: 3px 0;">Tanggal:</td>
                            <td style="text-align: right; font-weight: 500;">
                                {{ $quotation->quotation_date->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #666; padding: 3px 0;">Berlaku Sampai:</td>
                            <td style="text-align: right; font-weight: 500;">
                                {{ $quotation->valid_until->format('d M Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if($quotation->subject)
            <p style="margin-bottom: 20px;"><strong>Perihal:</strong> {{ $quotation->subject }}</p>
        @endif

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
                @foreach($quotation->items as $index => $item)
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

        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="color: #666; padding: 8px 0;">Subtotal</td>
                            <td style="text-align: right; font-weight: 500;">Rp
                                {{ number_format($quotation->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @if($quotation->discount_amount > 0)
                            <tr>
                                <td style="color: #666; padding: 8px 0;">Diskon</td>
                                <td style="text-align: right; font-weight: 500; color: #dc2626;">- Rp
                                    {{ number_format($quotation->discount_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td style="color: #666; padding: 8px 0;">PPN</td>
                            <td style="text-align: right; font-weight: 500;">Rp
                                {{ number_format($quotation->tax_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr style="border-top: 2px solid #333;">
                            <td style="font-weight: bold; color: #7c3aed; padding-top: 12px; font-size: 14px;">Total
                            </td>
                            <td
                                style="text-align: right; font-weight: bold; color: #7c3aed; padding-top: 12px; font-size: 14px;">
                                Rp {{ number_format($quotation->total, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if($quotation->notes || $quotation->terms)
            <div style="margin-top: 30px; padding: 15px; background-color: #f3f4f6; border-radius: 8px;">
                @if($quotation->notes)
                    <div style="margin-bottom: 15px;">
                        <h3 style="font-size: 11px; text-transform: uppercase; color: #666; margin-bottom: 8px;">Catatan</h3>
                        <p style="color: #333; font-size: 11px;">{{ $quotation->notes }}</p>
                    </div>
                @endif
                @if($quotation->terms)
                    <div>
                        <h3 style="font-size: 11px; text-transform: uppercase; color: #666; margin-bottom: 8px;">Syarat &
                            Ketentuan</h3>
                        <p style="color: #333; font-size: 11px;">{{ $quotation->terms }}</p>
                    </div>
                @endif
            </div>
        @endif

        <table style="width: 100%; margin-top: 50px;">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    @if($quotation->include_qr)
                        <div style="color: #666; font-size: 10px;">
                            <p style="margin-bottom: 5px;">Scan barcode untuk verifikasi keaslian dokumen:</p>
                            <img src="data:image/svg+xml;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(80)->generate(route('verify.quotation', $quotation->verification_code))) !!}"
                                alt="QR Verification">
                            <p style="margin-top: 5px;">Kode: <strong>{{ $quotation->quotation_number }}</strong></p>
                        </div>
                    @endif
                </td>
                <td style="width: 40%; text-align: center; vertical-align: top;">
                    <p style="margin-bottom: 5px; font-size: 11px;">{{ $quotation->tenant->city }},
                        {{ $quotation->quotation_date->format('d M Y') }}
                    </p>
                    <p style="font-weight: bold; margin-bottom: 20px; font-size: 11px;">
                        {{ $quotation->tenant->company_name }}
                    </p>

                    <div style="position: relative; height: 90px; width: 100%; margin-bottom: 10px;">
                        @if($quotation->include_stamp && $quotation->tenant->stamp_image && file_exists(public_path('storage/' . $quotation->tenant->stamp_image)))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $quotation->tenant->stamp_image))) }}"
                                style="height: 80px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 1; opacity: 0.8;">
                        @endif

                        @if($quotation->include_signature && $quotation->tenant->signature_image && file_exists(public_path('storage/' . $quotation->tenant->signature_image)))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $quotation->tenant->signature_image))) }}"
                                style="height: 80px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 2;">
                        @endif
                    </div>

                    <p style="font-size: 11px; text-decoration: underline; font-weight: bold;">
                        {{ $quotation->creator->name ?? 'Admin' }}
                    </p>
                    <p style="font-size: 10px;">Authorized Signature</p>
                </td>
            </tr>
        </table>

        <div
            style="margin-top: 30px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #eee; padding-top: 20px;">
            <p>Dokumen ini dibuat secara otomatis oleh sistem Paperly.</p>
            <p style="margin-top: 5px;">Kode Verifikasi: {{ $quotation->quotation_number }}</p>
            <p style="margin-top: 5px;">Link Verifikasi: {{ route('verify.quotation', $quotation->verification_code) }}
            </p>
        </div>
    </div>
</body>

</html>
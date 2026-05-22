<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    @php
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
    @endphp
    <style>
        @page {
            margin: 30px 40px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            color: #334155;
            line-height: 1.5;
            background: #ffffff;
        }

        .container {
            width: 100%;
        }

        /* =========================
            HEADER
        ========================= */
        .top-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .top-header td {
            vertical-align: top;
        }

        .company-section {
            width: 60%;
        }

        .document-section {
            width: 40%;
            text-align: right;
        }

        .logo {
            max-height: 70px;
            max-width: 200px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .company-detail {
            color: #64748b;
            font-size: 11px;
            line-height: 1.5;
        }

        .quotation-title {
            font-size: 32px;
            font-weight: bold;
            color: #0284c7; /* Biru Profesional */
            letter-spacing: 2px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .quotation-number {
            font-size: 14px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 10px;
        }

        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-pending {
            background: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
        }
        
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .blue-line {
            height: 4px;
            background: #0284c7;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        /* =========================
            CLIENT + INFO (BOXES)
        ========================= */
        .info-wrapper {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .info-wrapper td {
            vertical-align: top;
        }

        .client-card,
        .detail-card {
            border: 1px solid #bae6fd;
            background: #f0f9ff;
            padding: 15px;
            border-radius: 6px; 
        }

        .client-card {
            width: 55%;
        }

        .detail-card {
            width: 40%;
        }

        .spacer {
            width: 5%;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0284c7;
            margin-bottom: 10px;
            border-bottom: 1px solid #bae6fd;
            padding-bottom: 5px;
        }

        .client-name {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .client-detail {
            color: #475569;
            line-height: 1.5;
            font-size: 11px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table td {
            padding: 4px 0;
            font-size: 11px;
        }

        .detail-table td:first-child {
            color: #64748b;
        }

        .detail-table td:last-child {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }

        /* =========================
            SUBJECT
        ========================= */
        .subject-box {
            background: #f8fafc;
            border-left: 4px solid #0284c7;
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }

        .subject-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }

        .subject-text {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
        }

        /* =========================
            TABLE
        ========================= */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .items-table thead th {
            background: #0284c7;
            color: #ffffff;
            padding: 10px;
            font-size: 11px;
            text-transform: uppercase;
            border: 1px solid #0284c7;
            text-align: left;
        }

        .items-table tbody td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: top;
            font-size: 11px;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* =========================
            TOTAL
        ========================= */
        .total-wrapper {
            width: 100%;
            margin-bottom: 30px;
        }

        .total-box {
            width: 300px;
            float: right;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            background: #f0f9ff;
        }

        .total-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-box td {
            padding: 8px 12px;
            border-bottom: 1px solid #bae6fd;
            font-size: 11px;
        }

        .total-box tr:last-child td {
            border-bottom: none;
        }

        .total-box td:first-child {
            color: #475569;
            font-weight: bold;
        }

        .total-box td:last-child {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }

        .grand-total {
            background: #0284c7;
        }

        .grand-total td {
            color: #ffffff !important;
            font-size: 14px !important;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* =========================
            NOTES
        ========================= */
        .notes-wrapper {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 30px;
            clear: both;
        }

        .notes-block {
            margin-bottom: 15px;
        }

        .notes-block:last-child {
            margin-bottom: 0;
        }

        .notes-title {
            font-size: 11px;
            font-weight: bold;
            color: #0284c7;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .notes-content {
            color: #475569;
            line-height: 1.5;
            font-size: 11px;
        }

        /* =========================
            FOOT SECTION
        ========================= */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .bottom-table td {
            vertical-align: top;
        }

        /* QR */
        .qr-box {
            width: 180px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }

        .qr-title {
            font-size: 10px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .qr-box img {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }

        .qr-code {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }

        .qr-text {
            margin-top: 5px;
            color: #64748b;
            font-size: 9px;
            line-height: 1.4;
        }

        /* SIGNATURE */
        .signature-wrapper {
            width: 250px;
            float: right;
            text-align: center;
        }

        .signature-date {
            color: #475569;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .signature-company {
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .signature-area {
            position: relative;
            height: 100px;
            margin-bottom: 5px;
        }

        .stamp-image {
            position: absolute;
            left: 50%;
            top: 5px;
            transform: translateX(-50%);
            height: 85px;
            opacity: 0.75;
            z-index: 1;
        }

        .signature-image {
            position: absolute;
            left: 50%;
            top: 10px;
            transform: translateX(-50%);
            height: 80px;
            z-index: 2;
        }

        .signature-line {
            border-top: 1px solid #94a3b8;
            padding-top: 5px;
            width: 100%;
            margin: 0 auto;
        }

        .signature-name {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
        }

        .signature-role {
            color: #64748b;
            font-size: 10px;
        }

        /* =========================
            FOOTER
        ========================= */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
            line-height: 1.5;
            clear: both;
        }

    </style>
</head>

<body>

    <div class="container">

        <!-- HEADER -->
        <table class="top-header">
            <tr>
                <td class="company-section">
                    @if($quotation->tenant->logo && file_exists(public_path('storage/' . $quotation->tenant->logo)))
                        <img class="logo"
                            src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $quotation->tenant->logo))) }}">
                    @else
                        <div class="company-name">
                            {{ $quotation->tenant->company_name }}
                        </div>
                    @endif
                    
                    @if($quotation->tenant->logo)
                        <div class="company-name" style="font-size: 14px; margin-top: 5px;">
                            {{ $quotation->tenant->company_name }}
                        </div>
                    @endif

                    <div class="company-detail">
                        {{ $quotation->tenant->address }}<br>
                        {{ $quotation->tenant->city }}, {{ $quotation->tenant->province }} {{ $quotation->tenant->postal_code }}<br>
                        Telp: {{ $quotation->tenant->phone }} | Email: {{ $quotation->tenant->email }}
                        @if($quotation->tenant->npwp)
                            <br>NPWP: {{ $quotation->tenant->npwp }}
                        @endif
                    </div>
                </td>
                <td class="document-section">
                    <div class="quotation-title">
                        QUOTATION
                    </div>
                    <div class="quotation-number">
                        #{{ $quotation->quotation_number }}
                    </div>
                    <span class="status {{ $quotation->status == 'approved' ? 'status-approved' : ($quotation->status == 'rejected' ? 'status-rejected' : 'status-pending') }}">
                        {{ strtoupper($quotation->status) }}
                    </span>
                </td>
            </tr>
        </table>

        <div class="blue-line"></div>

        <!-- CLIENT & DETAILS -->
        <table class="info-wrapper">
            <tr>
                <td class="client-card">
                    <div class="section-title">
                        Ditujukan Kepada
                    </div>
                    <div class="client-name">
                        {{ $quotation->client->name }}
                    </div>
                    <div class="client-detail">
                        {{ $quotation->client->address }}<br>
                        {{ $quotation->client->city }},
                        {{ $quotation->client->province }}
                        @if($quotation->client->npwp)
                            <br>NPWP: {{ $quotation->client->npwp }}
                        @endif
                    </div>
                </td>
                <td class="spacer"></td>
                <td class="detail-card">
                    <div class="section-title">
                        Detail Quotation
                    </div>
                    <table class="detail-table">
                        <tr>
                            <td>Tanggal Dokumen</td>
                            <td>{{ $quotation->quotation_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td>Berlaku Sampai</td>
                            <td style="color: {{ $quotation->valid_until < now() && $quotation->status == 'pending' ? '#dc2626' : '#0f172a' }}">
                                {{ $quotation->valid_until->format('d M Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- SUBJECT -->
        @if($quotation->subject)
        <div class="subject-box">
            <div class="subject-label">
                Perihal
            </div>
            <div class="subject-text">
                {{ $quotation->subject }}
            </div>
        </div>
        @endif

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="40%">Deskripsi</th>
                    <th width="10%" class="text-right">Qty</th>
                    <th width="20%" class="text-right">Harga Satuan</th>
                    <th width="10%" class="text-right">Pajak</th>
                    <th width="15%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                <tr>
                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>
                    <td>
                        {{ $item->description }}
                    </td>
                    <td class="text-right">
                        {{ number_format($item->quantity, 0) }}
                        {{ $item->unit }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($item->tax_percent, 0) }}%
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOTAL -->
        <div class="total-wrapper clearfix">
            <div class="total-box">
                <table>
                    <tr>
                        <td>Subtotal</td>
                        <td>Rp {{ number_format($quotation->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($quotation->discount_amount > 0)
                    <tr>
                        <td>Diskon</td>
                        <td style="color: #dc2626;">- Rp {{ number_format($quotation->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>PPN</td>
                        <td>Rp {{ number_format($quotation->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL</td>
                        <td>Rp {{ number_format($quotation->total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- NOTES -->
        @if($quotation->notes || $quotation->terms)
        <div class="notes-wrapper">
            @if($quotation->notes)
            <div class="notes-block">
                <div class="notes-title">
                    Catatan
                </div>
                <div class="notes-content">
                    {{ $quotation->notes }}
                </div>
            </div>
            @endif

            @if($quotation->terms)
            <div class="notes-block">
                <div class="notes-title">
                    Syarat & Ketentuan
                </div>
                <div class="notes-content">
                    {{ $quotation->terms }}
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- QR + SIGNATURE -->
        <table class="bottom-table">
            <tr>
                <td style="width: 50%;">
                    @if($quotation->include_qr)
                    <div class="qr-box">
                        <div class="qr-title">
                            Verifikasi Dokumen
                        </div>
                        <img src="data:image/svg+xml;base64,{!! base64_encode(
                            QrCode::format('svg')
                            ->size(80)
                            ->generate(route('verify.quotation', $quotation->verification_code ?? $quotation->quotation_number))
                        ) !!}">
                        <div class="qr-code">
                            {{ $quotation->quotation_number }}
                        </div>
                        <div class="qr-text">
                            Scan barcode untuk verifikasi keaslian dokumen quotation ini.
                        </div>
                    </div>
                    @endif
                </td>
                <td style="width: 50%;">
                    <div class="signature-wrapper">
                        <div class="signature-date">
                            {{ $quotation->tenant->city }},
                            {{ $quotation->quotation_date->format('d F Y') }}
                        </div>
                        <div class="signature-company">
                            {{ $quotation->tenant->company_name }}
                        </div>

                        <div class="signature-area">
                            @if($quotation->include_stamp && $quotation->tenant->stamp_image && file_exists(public_path('storage/' . $quotation->tenant->stamp_image)))
                                <img class="stamp-image"
                                    src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $quotation->tenant->stamp_image))) }}">
                            @endif

                            @if($quotation->include_signature && $quotation->tenant->signature_image && file_exists(public_path('storage/' . $quotation->tenant->signature_image)))
                                <img class="signature-image"
                                    src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $quotation->tenant->signature_image))) }}">
                            @endif
                        </div>

                        <div class="signature-line">
                            <div class="signature-name">
                                @if($quotation->include_signature)
                                    {{ $quotation->creator->name ?? 'Admin' }}
                                @endif
                            </div>
                            <div class="signature-role">
                                Authorized Signature
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <div class="footer">
            Dokumen ini dibuat secara otomatis oleh sistem {{ config('app.name') }}.<br>
            @if($quotation->include_qr)
            Link Verifikasi: {{ route('verify.quotation', $quotation->verification_code ?? $quotation->quotation_number) }}
            @endif
        </div>

    </div>

</body>
</html>
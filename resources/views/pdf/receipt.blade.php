<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi {{ $receipt->receipt_number }}</title>
    <style>
        @page {
            margin: 8mm;
            size: 210mm 148mm; /* A5 Landscape */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.4;
        }

        .kwitansi {
            border: 3px double #000;
            padding: 8mm 10mm;
            position: relative;
            height: 100%;
        }

        /* ============ WATERMARK ============ */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 54pt;
            font-weight: bold;
            color: rgba(0, 128, 0, 0.08);
            text-transform: uppercase;
            letter-spacing: 10px;
            z-index: 0;
            pointer-events: none;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        /* ============ HEADER ============ */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell {
            width: 55mm;
        }

        .logo-cell img {
            max-height: 36px;
            display: block;
        }

        .company-name {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .company-detail {
            font-size: 8pt;
            color: #333;
            line-height: 1.3;
        }

        .title-cell {
            text-align: right;
            width: 70mm;
        }

        .title-kwitansi {
            font-size: 22pt;
            font-weight: bold;
            letter-spacing: 5px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 2px;
            display: inline-block;
        }

        .nomor-tanggal {
            font-size: 9pt;
            margin-top: 3px;
            text-align: right;
        }

        .nomor-tanggal .nomor {
            color: #c00;
            font-weight: bold;
            font-size: 10pt;
        }

        .header-line {
            border: none;
            border-top: 2px solid #000;
            margin: 3mm 0;
        }

        /* ============ CONTENT ROWS ============ */
        .field-table {
            width: 100%;
            border-collapse: collapse;
        }

        .field-table td {
            padding: 2.5mm 0;
            vertical-align: top;
            font-size: 11pt;
        }

        .field-label {
            width: 38mm;
            font-weight: bold;
            white-space: nowrap;
        }

        .field-colon {
            width: 5mm;
            text-align: center;
        }

        .field-value {
            border-bottom: 1px dotted #555;
        }

        .field-value-strong {
            border-bottom: 1px dotted #555;
            font-weight: bold;
            font-size: 12pt;
        }

        /* ============ AMOUNT BOX ============ */
        .amount-box {
            border: 2px solid #000;
            margin: 4mm 0;
            display: table;
            width: 100%;
        }

        .amount-box-label {
            display: table-cell;
            width: 38mm;
            background: #e8e8e8;
            border-right: 2px solid #000;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            font-size: 11pt;
            padding: 3mm 2mm;
        }

        .amount-box-value {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            padding: 3mm 5mm;
        }

        .amount-number {
            font-size: 18pt;
            font-weight: bold;
        }

        .amount-words {
            font-size: 9.5pt;
            font-style: italic;
            margin-top: 1mm;
            color: #333;
        }

        /* ============ PAYMENT INFO ============ */
        .payment-row {
            font-size: 9pt;
            color: #444;
            margin-top: 2mm;
            padding: 2mm 0;
            border-top: 1px solid #ccc;
        }

        .notes-row {
            font-size: 9pt;
            color: #555;
            margin-top: 1mm;
        }

        /* ============ SIGNATURE ============ */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5mm;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 15mm;
        }

        .sig-title {
            font-size: 9pt;
            color: #555;
            margin-bottom: 1mm;
        }

        .sig-space {
            height: 18mm;
            position: relative;
        }

        .sig-line {
            border-bottom: 1px solid #000;
            margin: 0 5mm;
        }

        .sig-name {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 2mm;
        }

        .stamp-img {
            position: absolute;
            right: 0;
            bottom: 0;
            opacity: 0.75;
            z-index: 1;
        }

        .stamp-img img {
            max-width: 70px;
            max-height: 70px;
        }

        .signature-img {
            position: absolute;
            bottom: 2mm;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }

        .signature-img img {
            height: 45px;
        }

        /* ============ FOOTER ============ */
        .footer-section {
            margin-top: 3mm;
            border-top: 1px solid #ddd;
            padding-top: 2mm;
            font-size: 7.5pt;
            color: #999;
            display: table;
            width: 100%;
        }

        .footer-qr {
            display: table-cell;
            width: 50px;
            vertical-align: middle;
            text-align: center;
        }

        .footer-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 3mm;
        }
    </style>
</head>
<body>
    @php
        $tenant = $receipt->invoice->tenant;

        // Convert amount to words (Indonesian)
        function terbilang($angka) {
            $angka = abs($angka);
            $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
            $temp = "";

            if ($angka < 12) {
                $temp = " " . $huruf[$angka];
            } elseif ($angka < 20) {
                $temp = terbilang($angka - 10) . " Belas";
            } elseif ($angka < 100) {
                $temp = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
            } elseif ($angka < 200) {
                $temp = " Seratus" . terbilang($angka - 100);
            } elseif ($angka < 1000) {
                $temp = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
            } elseif ($angka < 2000) {
                $temp = " Seribu" . terbilang($angka - 1000);
            } elseif ($angka < 1000000) {
                $temp = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
            } elseif ($angka < 1000000000) {
                $temp = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
            } elseif ($angka < 1000000000000) {
                $temp = terbilang($angka / 1000000000) . " Miliar" . terbilang(fmod($angka, 1000000000));
            } elseif ($angka < 1000000000000000) {
                $temp = terbilang($angka / 1000000000000) . " Triliun" . terbilang(fmod($angka, 1000000000000));
            }

            return $temp;
        }

        $amountInWords = trim(terbilang(floor($receipt->amount))) . " Rupiah";

        // Payment method label
        $paymentMethodLabel = '-';
        if ($receipt->payment) {
            switch ($receipt->payment->payment_method) {
                case 'cash': $paymentMethodLabel = 'Tunai'; break;
                case 'transfer': $paymentMethodLabel = 'Transfer Bank'; break;
                case 'check': $paymentMethodLabel = 'Cek/Giro'; break;
                case 'qris': $paymentMethodLabel = 'QRIS'; break;
                default: $paymentMethodLabel = ucfirst($receipt->payment->payment_method);
            }
        }
    @endphp

    <div class="kwitansi">
        {{-- Watermark --}}
        <div class="watermark">LUNAS</div>

        <div class="content">
            {{-- ===== HEADER ===== --}}
            <table class="header-table">
                <tr>
                    <td>
                        @if($tenant->logo && file_exists(public_path('storage/' . $tenant->logo)))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $tenant->logo))) }}"
                                 alt="Logo" style="max-height: 32px; margin-bottom: 2px;">
                        @endif
                        <div class="company-name">{{ $tenant->company_name }}</div>
                        <div class="company-detail">
                            {{ $tenant->address }}<br>
                            {{ $tenant->city }} {{ $tenant->postal_code }}
                            &bull; Telp: {{ $tenant->phone }}
                            @if($tenant->npwp)<br>NPWP: {{ $tenant->npwp }}@endif
                        </div>
                    </td>
                    <td class="title-cell">
                        <div class="title-kwitansi">KWITANSI</div>
                        <div class="nomor-tanggal">
                            No: <span class="nomor">{{ $receipt->receipt_number }}</span><br>
                            Tanggal: {{ $receipt->receipt_date->format('d / m / Y') }}
                        </div>
                    </td>
                </tr>
            </table>

            <hr class="header-line">

            {{-- ===== FIELD ROWS ===== --}}
            <table class="field-table">
                <tr>
                    <td class="field-label">Sudah Terima Dari</td>
                    <td class="field-colon">:</td>
                    <td class="field-value-strong">{{ $receipt->invoice->client->name }}</td>
                </tr>
                <tr>
                    <td class="field-label">Alamat</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">{{ $receipt->invoice->client->address ?? '-' }}</td>
                </tr>
            </table>

            {{-- ===== AMOUNT BOX ===== --}}
            <div class="amount-box">
                <div class="amount-box-label">Uang<br>Sejumlah</div>
                <div class="amount-box-value">
                    <div class="amount-number">Rp {{ number_format($receipt->amount, 0, ',', '.') }},-</div>
                    <div class="amount-words"># {{ $amountInWords }} #</div>
                </div>
            </div>

            {{-- ===== UNTUK PEMBAYARAN ===== --}}
            <table class="field-table">
                <tr>
                    <td class="field-label">Untuk Pembayaran</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">
                        Invoice No. <strong>{{ $receipt->invoice->invoice_number }}</strong>
                        @if($receipt->invoice->subject)
                            &mdash; {{ $receipt->invoice->subject }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="field-label">Metode Bayar</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">
                        {{ $paymentMethodLabel }}
                        @if($receipt->payment && $receipt->payment->reference_number)
                            &nbsp;&bull;&nbsp; Ref: {{ $receipt->payment->reference_number }}
                        @endif
                    </td>
                </tr>
            </table>

            {{-- ===== NOTES ===== --}}
            @if($receipt->notes)
            <div class="notes-row">
                <strong>Catatan:</strong> {{ $receipt->notes }}
            </div>
            @endif

            {{-- ===== SIGNATURE AREA ===== --}}
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="sig-title">Penerima</div>
                        <div class="sig-space"></div>
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $receipt->invoice->client->name }}</div>
                    </td>
                    <td>
                        <div class="sig-title">{{ $tenant->city }}, {{ $receipt->receipt_date->format('d F Y') }}</div>
                        <div class="sig-space" style="position: relative;">
                            @if($receipt->include_stamp && $tenant->stamp_image && file_exists(public_path('storage/' . $tenant->stamp_image)))
                                <div class="stamp-img">
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $tenant->stamp_image))) }}">
                                </div>
                            @endif

                            @if($receipt->include_signature && $tenant->signature_image && file_exists(public_path('storage/' . $tenant->signature_image)))
                                <div class="signature-img">
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $tenant->signature_image))) }}">
                                </div>
                            @endif
                        </div>
                        <div class="sig-line"></div>
                        @if($receipt->include_signature)
                            <div class="sig-name">{{ $tenant->company_name }}</div>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- ===== FOOTER ===== --}}
            <div class="footer-section">
                <div class="footer-qr">
                    @if($receipt->include_qr)
                        <img src="data:image/svg+xml;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(45)->generate(route('verify.receipt', $receipt->verification_code))) !!}" alt="QR">
                    @endif
                </div>
                <div class="footer-text">
                    Dokumen ini dibuat secara otomatis oleh sistem {{ config('app.name') }}.
                    @if($receipt->include_qr)
                        <br>Verifikasi: {{ route('verify.receipt', $receipt->verification_code) }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi {{ $receipt->receipt_number }}</title>
    <style>
        @page {
            margin: 10mm 10mm;
            size: 210mm 148mm; /* A5 Landscape */
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.3;
        }
        
        .receipt-container {
            border: 3px double #000;
            padding: 10px 15px;
            position: relative;
            height: 100%; /* Changed from min-height: 100% to avoid overflow issues */
        }
        
        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }
        
        .header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }
        
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .company-info {
            font-size: 9pt;
            color: #333;
        }
        
        .receipt-title {
            font-size: 24pt;
            font-weight: bold;
            letter-spacing: 3px;
            color: #000;
        }
        
        /* Receipt Number */
        .receipt-number-box {
            text-align: right;
            margin-bottom: 15px;
        }
        
        .receipt-number-label {
            font-size: 10pt;
            color: #666;
        }
        
        .receipt-number {
            font-size: 12pt;
            font-weight: bold;
            color: #c00;
        }
        
        /* Main Content */
        .content-row {
            margin-bottom: 12px;
        }
        
        .row-table {
            width: 100%;
            display: table;
        }
        
        .row-label {
            display: table-cell;
            width: 140px;
            font-weight: bold;
            padding-right: 10px;
            vertical-align: top;
        }
        
        .row-colon {
            display: table-cell;
            width: 15px;
            vertical-align: top;
        }
        
        .row-value {
            display: table-cell;
            border-bottom: 1px dotted #666;
            padding-bottom: 2px;
        }
        
        /* Amount Box */
        .amount-box {
            background: #f5f5f5;
            border: 2px solid #000;
            padding: 12px 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        .amount-label {
            font-size: 10pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .amount-value {
            font-size: 24pt;
            font-weight: bold;
            color: #000;
            margin: 5px 0;
        }
        
        .amount-words {
            font-size: 10pt;
            font-style: italic;
            color: #333;
        }
        
        /* Payment Info */
        .payment-info {
            margin-top: 15px;
            padding: 10px;
            background: #fafafa;
            border: 1px solid #ddd;
        }
        
        .payment-title {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }
        
        /* Signature Area */
        .signature-area {
            display: table;
            width: 100%;
            margin-top: 25px;
        }
        
        .signature-left {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        
        .signature-box {
            border-bottom: 1px solid #000;
            height: 50px;
            margin: 0 30px 5px 30px;
            position: relative;
        }
        
        .signature-label {
            font-size: 9pt;
            color: #666;
        }
        
        .signature-name {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .stamp-area {
            position: absolute;
            right: 30px;
            bottom: -10px;
            opacity: 0.7;
        }
        
        .stamp-area img {
            max-width: 80px;
            max-height: 80px;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        /* Watermark for PAID */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 60pt;
            font-weight: bold;
            color: rgba(0, 128, 0, 0.1);
            text-transform: uppercase;
            letter-spacing: 10px;
            z-index: 0;
            pointer-events: none;
        }
        
        .content {
            position: relative;
            z-index: 1;
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
    @endphp

    <div class="receipt-container">
        <!-- Watermark -->
        <div class="watermark">LUNAS</div>
        
        <div class="content">
            <!-- Header -->
            <div class="header">
                <div class="header-left">
                    @if($tenant->logo && file_exists(public_path('storage/' . $tenant->logo)))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $tenant->logo))) }}" 
                             alt="Logo" style="max-height: 50px; margin-bottom: 5px;">
                        <div class="company-name" style="font-size: 14pt;">{{ $tenant->company_name }}</div>
                    @else
                        <div class="company-name">{{ $tenant->company_name }}</div>
                    @endif
                    
                    <div class="company-info">
                        {{ $tenant->address }}<br>
                        {{ $tenant->city }} {{ $tenant->postal_code }}<br>
                        Telp: {{ $tenant->phone }} | Email: {{ $tenant->email }}
                        @if($tenant->npwp)<br>NPWP: {{ $tenant->npwp }}@endif
                    </div>
                </div>
                <div class="header-right">
                    <div class="receipt-title">KWITANSI</div>
                </div>
            </div>

            <!-- Receipt Number & Date -->
            <div class="receipt-number-box">
                <span class="receipt-number-label">No. </span>
                <span class="receipt-number">{{ $receipt->receipt_number }}</span>
                <br>
                <span style="font-size: 10pt;">{{ $receipt->receipt_date->format('d F Y') }}</span>
            </div>

            <!-- Content Rows -->
            <div class="content-row">
                <div class="row-table">
                    <span class="row-label">Sudah Terima Dari</span>
                    <span class="row-colon">:</span>
                    <span class="row-value"><strong>{{ $receipt->invoice->client->name }}</strong></span>
                </div>
            </div>

            <div class="content-row">
                <div class="row-table">
                    <span class="row-label">Alamat</span>
                    <span class="row-colon">:</span>
                    <span class="row-value">{{ $receipt->invoice->client->address ?? '-' }}</span>
                </div>
            </div>

            <!-- Amount Box -->
            <div class="amount-box">
                <div class="amount-label">Uang Sejumlah</div>
                <div class="amount-value">Rp {{ number_format($receipt->amount, 0, ',', '.') }}</div>
                <div class="amount-words"># {{ $amountInWords }} #</div>
            </div>

            <div class="content-row">
                <div class="row-table">
                    <span class="row-label">Untuk Pembayaran</span>
                    <span class="row-colon">:</span>
                    <span class="row-value">
                        Invoice No. <strong>{{ $receipt->invoice->invoice_number }}</strong>
                        @if($receipt->invoice->subject)
                            <br>{{ $receipt->invoice->subject }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- Payment Info -->
            @if($receipt->payment)
            <div class="payment-info">
                <div class="payment-title">Informasi Pembayaran</div>
                <div style="font-size: 10pt;">
                    Metode: 
                    @switch($receipt->payment->payment_method)
                        @case('cash') Tunai @break
                        @case('transfer') Transfer Bank @break
                        @case('check') Cek/Giro @break
                        @case('qris') QRIS @break
                        @default {{ ucfirst($receipt->payment->payment_method) }}
                    @endswitch
                    @if($receipt->payment->reference_number)
                        &nbsp;&nbsp;|&nbsp;&nbsp; Ref: {{ $receipt->payment->reference_number }}
                    @endif
                </div>
            </div>
            @endif

            @if($receipt->notes)
            <div style="margin-top: 10px; font-size: 9pt; color: #666;">
                <strong>Catatan:</strong> {{ $receipt->notes }}
            </div>
            @endif

            <!-- Signature Area -->
            <div class="signature-area">
                <div class="signature-left">
                    <div class="signature-label">Penerima</div>
                    <div class="signature-box"></div>
                    <div class="signature-name">{{ $receipt->invoice->client->name }}</div>
                    
                </div>
                <div class="signature-right">
                    <div class="signature-label">{{ $tenant->city }}, {{ $receipt->receipt_date->format('d F Y') }}</div>
                    <div class="signature-box" style="position: relative; height: 80px;">
                        @if($receipt->include_stamp && $tenant->stamp_image && file_exists(public_path('storage/' . $tenant->stamp_image)))
                            <div class="stamp-area" style="position: absolute; right: 10px; bottom: 10px; opacity: 0.8; z-index: 1;">
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $tenant->stamp_image))) }}" 
                                     style="max-width: 80px; max-height: 80px;">
                            </div>
                        @endif

                        @if($receipt->include_signature && $tenant->signature_image && file_exists(public_path('storage/' . $tenant->signature_image)))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $tenant->signature_image))) }}" 
                                 style="height: 60px; position: absolute; bottom: 5px; left: 50%; transform: translateX(-50%); z-index: 2;">
                        @endif
                    </div>
                    @if($receipt->include_signature)
                        <div class="signature-name">{{ $tenant->company_name }}</div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="footer" style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: right; width: 45%; padding-right: 15px; vertical-align: middle;">
                            @if($receipt->include_qr)
                                <img src="data:image/svg+xml;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->generate(route('verify.receipt', $receipt->verification_code))) !!}" alt="QR Verification">
                            @endif
                        </td>
                        <td style="text-align: left; width: 55%; vertical-align: middle; font-size: 8pt; color: #999;">
                            <p>Dokumen ini dibuat secara otomatis oleh sistem Paperly.</p>
                            @if($receipt->include_qr)
                                <p style="margin-top: 5px;">Kode Verifikasi: {{ $receipt->receipt_number }}</p>
                                <p style="margin-top: 5px;">Link Verifikasi: {{ route('verify.receipt', $receipt->verification_code) }}</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

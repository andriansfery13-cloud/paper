<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice dari {{ $tenant->company_name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
        }

        .content {
            margin-bottom: 30px;
        }

        .highlight-box {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            color: #666;
        }

        .detail-value {
            font-weight: 600;
        }

        .total {
            font-size: 24px;
            color: #10b981;
            font-weight: bold;
        }

        .due-date {
            color: #dc2626;
            font-weight: bold;
        }

        .button {
            display: inline-block;
            background-color: #10b981;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #059669;
        }

        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .warning-box {
            background-color: #fef2f2;
            border: 1px solid #dc2626;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .bank-info {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $tenant->company_name }}</div>
            <p style="color: #666; margin: 5px 0 0;">{{ $tenant->address }}, {{ $tenant->city }}</p>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $invoice->client->name }}</strong>,</p>

            <p>Bersama email ini kami kirimkan invoice untuk transaksi Anda:</p>

            <div class="highlight-box">
                <div class="detail-row">
                    <span class="detail-label">No. Invoice</span>
                    <span class="detail-value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Invoice</span>
                    <span class="detail-value">{{ $invoice->invoice_date->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jatuh Tempo</span>
                    <span class="due-date">{{ $invoice->due_date->format('d M Y') }}</span>
                </div>
                @if($invoice->subject)
                    <div class="detail-row">
                        <span class="detail-label">Perihal</span>
                        <span class="detail-value">{{ $invoice->subject }}</span>
                    </div>
                @endif
                <div class="detail-row" style="border-bottom: none; padding-top: 15px;">
                    <span class="detail-label">Total Tagihan</span>
                    <span class="total">{{ $invoice->formatted_total }}</span>
                </div>
                @if($invoice->amount_paid > 0)
                    <div class="detail-row">
                        <span class="detail-label">Sudah Dibayar</span>
                        <span class="detail-value" style="color: #10b981;">Rp
                            {{ number_format($invoice->amount_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sisa Tagihan</span>
                        <span class="detail-value" style="color: #dc2626;">Rp
                            {{ number_format($invoice->amount_due, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <p>Silakan periksa lampiran PDF untuk detail lengkap invoice ini.</p>

            @if($invoice->amount_due > 0)
                <div class="warning-box">
                    <strong>⚠️ Perhatian:</strong><br>
                    Mohon lakukan pembayaran sebelum tanggal jatuh tempo
                    <strong>{{ $invoice->due_date->format('d M Y') }}</strong>
                    untuk menghindari keterlambatan.
                </div>
            @endif

            <div class="bank-info">
                <strong>💳 Informasi Pembayaran:</strong><br>
                <p style="margin: 10px 0 0;">Silakan hubungi kami untuk informasi rekening pembayaran atau gunakan link
                    pembayaran online jika tersedia.</p>
            </div>

            <p>Jika Anda sudah melakukan pembayaran, mohon abaikan email ini.</p>

            <p>
                Salam hangat,<br>
                <strong>{{ $tenant->company_name }}</strong><br>
                {{ $tenant->phone }}<br>
                {{ $tenant->email }}
            </p>
        </div>

        <div class="footer">
            @if($footer)
                <p>{{ $footer }}</p>
            @endif
            <p>Email ini dikirim secara otomatis dari sistem {{ config('app.name') }}.</p>
            @if($invoice->verification_code)
                <p>Kode Verifikasi: {{ $invoice->verification_code }}</p>
            @endif
        </div>
    </div>
</body>

</html>
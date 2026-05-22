<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation dari {{ $tenant->company_name }}</title>
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
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
        }

        .content {
            margin-bottom: 30px;
        }

        .highlight-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
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
            color: #3b82f6;
            font-weight: bold;
        }

        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #2563eb;
        }

        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .note {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
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
            <p>Yth. <strong>{{ $quotation->client->name }}</strong>,</p>

            <p>Terima kasih atas minat Anda. Bersama email ini kami lampirkan penawaran untuk Anda:</p>

            <div class="highlight-box">
                <div class="detail-row">
                    <span class="detail-label">No. Quotation</span>
                    <span class="detail-value">{{ $quotation->quotation_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal</span>
                    <span class="detail-value">{{ $quotation->quotation_date->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Berlaku Sampai</span>
                    <span class="detail-value">{{ $quotation->valid_until->format('d M Y') }}</span>
                </div>
                @if($quotation->subject)
                    <div class="detail-row">
                        <span class="detail-label">Perihal</span>
                        <span class="detail-value">{{ $quotation->subject }}</span>
                    </div>
                @endif
                <div class="detail-row" style="border-bottom: none; padding-top: 15px;">
                    <span class="detail-label">Total Penawaran</span>
                    <span class="total">{{ $quotation->formatted_total }}</span>
                </div>
            </div>

            <p>Silakan periksa lampiran PDF untuk detail lengkap penawaran ini.</p>

            <div class="note">
                <strong>📌 Catatan Penting:</strong><br>
                Penawaran ini berlaku sampai tanggal <strong>{{ $quotation->valid_until->format('d M Y') }}</strong>.
                Mohon konfirmasi persetujuan Anda sebelum tanggal tersebut.
            </div>

            <p>Jika ada pertanyaan, jangan ragu untuk menghubungi kami.</p>

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
            @if($quotation->verification_code)
                <p>Kode Verifikasi: {{ $quotation->verification_code }}</p>
            @endif
        </div>
    </div>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran - {{ $tenant->company_name }}</title>
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

        .success-icon {
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }

        .content {
            margin-bottom: 30px;
        }

        .highlight-box {
            background-color: #ecfdf5;
            border: 2px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }

        .payment-amount {
            font-size: 32px;
            color: #10b981;
            font-weight: bold;
            margin: 10px 0;
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

        .info-box {
            background-color: #f9fafb;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .balance-info {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .balance-paid {
            background-color: #ecfdf5;
            border: 1px solid #10b981;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $tenant->company_name }}</div>
            <p style="color: #666; margin: 5px 0 0;">Konfirmasi Pembayaran</p>
        </div>

        <div class="success-icon">✅</div>

        <div class="content">
            <p>Yth. <strong>{{ $invoice->client->name ?? 'Pelanggan' }}</strong>,</p>

            <p>Kami telah menerima pembayaran Anda. Berikut adalah rincian pembayaran:</p>

            <div class="highlight-box">
                <p style="margin: 0; color: #666;">Pembayaran Diterima</p>
                <div class="payment-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                <p style="margin: 0; color: #666;">{{ $payment->payment_date->format('d M Y') }}</p>
            </div>

            <div class="info-box">
                <div class="detail-row">
                    <span class="detail-label">No. Invoice</span>
                    <span class="detail-value">{{ $invoice->invoice_number ?? '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Metode Pembayaran</span>
                    <span class="detail-value">{{ ucfirst($payment->payment_method ?? 'Transfer Bank') }}</span>
                </div>
                @if($payment->reference_number)
                    <div class="detail-row">
                        <span class="detail-label">No. Referensi</span>
                        <span class="detail-value">{{ $payment->reference_number }}</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Total Invoice</span>
                    <span class="detail-value">Rp {{ number_format($invoice->total ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Dibayar</span>
                    <span class="detail-value" style="color: #10b981;">Rp
                        {{ number_format($invoice->amount_paid ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row" style="border-bottom: none;">
                    <span class="detail-label">Sisa Tagihan</span>
                    <span class="detail-value"
                        style="color: {{ ($invoice->amount_due ?? 0) > 0 ? '#dc2626' : '#10b981' }};">
                        Rp {{ number_format($invoice->amount_due ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            @if(($invoice->amount_due ?? 0) > 0)
                <div class="balance-info">
                    <strong>📌 Informasi:</strong><br>
                    Masih terdapat sisa tagihan sebesar <strong>Rp
                        {{ number_format($invoice->amount_due, 0, ',', '.') }}</strong>.
                    Mohon selesaikan pembayaran sebelum tanggal jatuh tempo.
                </div>
            @else
                <div class="balance-info balance-paid">
                    <strong>🎉 Invoice Lunas!</strong><br>
                    Terima kasih, pembayaran untuk invoice ini sudah lunas. Kami menghargai kepercayaan Anda.
                </div>
            @endif

            <p>Terima kasih atas pembayaran Anda.</p>

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
        </div>
    </div>
</body>

</html>
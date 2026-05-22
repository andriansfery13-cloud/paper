<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;

class DocumentTemplateSeeder extends Seeder
{
    public function run()
    {
        // --------------------------------------------------------------------------------
        // INVOICES
        // --------------------------------------------------------------------------------

        // 1. Modern Invoice
        DocumentTemplate::updateOrCreate(
            ['name' => 'Modern Invoice', 'type' => 'invoice', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/e2e8f0/475569?text=Modern+Invoice',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; color: #555; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; line-height: 24px; color: #555; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .header { background: #f8f9fa; border-bottom: 2px solid #333; }
        .heading td { background: #333; color: #fff; font-weight: bold; }
        .total td { border-top: 2px solid #eee; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <h1 style="margin: 0;">INVOICE</h1>
                                <span style="font-size: 14px; color: #888;">#{{ $number }}</span>
                            </td>
                            <td>
                                <strong>{{ $tenant->company_name }}</strong><br>
                                {{ $tenant->address }}<br>
                                {{ $tenant->email }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Tagihan Ke:</strong><br>
                                {{ $client->name }}<br>
                                {{ $client->address }}
                            </td>
                            <td class="text-right">
                                <strong>Tanggal:</strong> {{ $date }}<br>
                                <strong>Jatuh Tempo:</strong> {{ $due_date }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Item</td>
                <td class="text-right">Harga</td>
            </tr>
            @foreach($items as $item)
            <tr class="item">
                <td>{{ $item->description }} <br><small>x{{ $item->quantity }} {{ $item->unit }}</small></td>
                <td class="text-right">{{ format_currency($item->total) }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td></td>
                <td class="text-right">Total: {{ format_currency($total) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML
                ,
                'is_default' => true
            ]
        );

        // 2. Classic Invoice
        DocumentTemplate::updateOrCreate(
            ['name' => 'Classic Invoice', 'type' => 'invoice', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/f1f5f9/475569?text=Classic+Invoice',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #999; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { margin: 0; font-size: 36px; text-transform: uppercase; letter-spacing: 2px; }
        .meta { margin-bottom: 30px; width: 100%; }
        .meta td { vertical-align: top; width: 50%; }
        .items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items th, .items td { border: 1px solid #999; padding: 8px; }
        .items th { background: #eee; text-align: left; }
        .totals { width: 100%; text-align: right; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h1>INVOICE</h1>
            <p>{{ $tenant->company_name }}</p>
        </div>
        <table class="meta">
            <tr>
                <td>
                    <strong>Kepada:</strong><br>
                    {{ $client->name }}<br>
                    {{ $client->address }}
                </td>
                <td style="text-align: right;">
                    <strong>No. Invoice:</strong> {{ $number }}<br>
                    <strong>Tanggal:</strong> {{ $date }}<br>
                    <strong>Jatuh Tempo:</strong> {{ $due_date }}
                </td>
            </tr>
        </table>
        <table class="items">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th style="width: 80px;">Qty</th>
                    <th style="width: 120px;">Harga Satuan</th>
                    <th style="width: 120px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ format_currency($item->price) }}</td>
                    <td>{{ format_currency($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="totals">
            <h3>Total: {{ format_currency($total) }}</h3>
        </div>
    </div>
</body>
</html>
HTML
            ]
        );

        // 3. Bold Invoice
        DocumentTemplate::updateOrCreate(
            ['name' => 'Bold Invoice', 'type' => 'invoice', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/1e293b/ffffff?text=Bold+Invoice',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; color: #000; }
        .invoice-box { max-width: 800px; margin: auto; }
        .header { background: #000; color: #fff; padding: 40px; }
        .header h1 { margin: 0; font-size: 40px; }
        .content { padding: 40px; }
        .items { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .items th { border-bottom: 4px solid #000; padding: 10px; text-align: left; }
        .items td { border-bottom: 1px solid #ddd; padding: 10px; }
        .total { margin-top: 30px; text-align: right; font-size: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <table width="100%">
                <tr>
                    <td>
                        <h1>INVOICE</h1>
                        <p>#{{ $number }}</p>
                    </td>
                    <td align="right">
                        {{ $tenant->company_name }}<br>
                        {{ $tenant->email }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="content">
            <p><strong>Ditujukan Kepada:</strong><br>
            {{ $client->name }}<br>
            {{ $client->address }}</p>
            
            <table class="items">
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ format_currency($item->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="total">
                Total Pembayaran: {{ format_currency($total) }}
            </div>
        </div>
    </div>
</body>
</html>
HTML
            ]
        );

        // --------------------------------------------------------------------------------
        // QUOTATIONS
        // --------------------------------------------------------------------------------

        // 1. Corporate Quotation
        DocumentTemplate::updateOrCreate(
            ['name' => 'Corporate Quotation', 'type' => 'quotation', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/3b82f6/ffffff?text=Corporate+Quote',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penawaran</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        .container { max-width: 800px; margin: auto; padding: 30px; }
        .header { border-bottom: 2px solid #2c3e50; padding-bottom: 20px; margin-bottom: 30px; }
        .title { color: #2c3e50; font-size: 28px; font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { background: #2c3e50; color: white; padding: 10px; text-align: left; }
        .items-table td { border-bottom: 1px solid #ecf0f1; padding: 10px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table width="100%">
                <tr>
                    <td class="title">PENAWARAN HARGA</td>
                    <td align="right">
                        <strong>{{ $tenant->company_name }}</strong><br>
                        {{ $date }}
                    </td>
                </tr>
            </table>
        </div>
        <p>Kepada Yth,<br><strong>{{ $client->name }}</strong></p>
        <p>Dengan hormat,<br>Berikut kami sampaikan penawaran harga:</p>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ format_currency($item->price) }}</td>
                    <td>{{ format_currency($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="text-align: right; margin-top: 20px;">
            <h3>Total Estimasi: {{ format_currency($total) }}</h3>
        </div>

        <div class="footer">
            Penawaran ini berlaku hingga {{ $valid_until }}. Terima kasih atas kerjasamanya.
        </div>
    </div>
</body>
</html>
HTML
                ,
                'is_default' => true
            ]
        );

        // 2. Simple Quotation
        DocumentTemplate::updateOrCreate(
            ['name' => 'Simple Quotation', 'type' => 'quotation', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/f8fafc/64748b?text=Simple+Quote',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .box { border: 1px solid #ccc; padding: 20px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #eee; padding: 8px; text-align: left; }
        th { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="box">
        <div class="title">QUOTATION #{{ $number }}</div>
        <div>
            Dari: {{ $tenant->company_name }}<br>
            Untuk: {{ $client->name }}
        </div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ format_currency($item->price) }}</td>
                    <td>{{ format_currency($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="right"><strong>Valid Hingga {{ $valid_until }}</strong></td>
                    <td><strong>{{ format_currency($total) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
HTML
            ]
        );

        // 3. Creative Quotation
        DocumentTemplate::updateOrCreate(
            ['name' => 'Creative Quotation', 'type' => 'quotation', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/8b5cf6/ffffff?text=Creative+Quote',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation</title>
    <style>
        body { font-family: 'Trebuchet MS', sans-serif; color: #444; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px 10px 0 0; }
        .body { background: white; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 10px 10px; }
        h1 { margin: 0; }
        .item-row { border-bottom: 1px solid #eee; padding: 10px 0; display: flex; justify-content: space-between; }
        .total-row { background: #f4f7fa; padding: 15px; margin-top: 20px; border-radius: 8px; text-align: right; font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <div style="max-width: 800px; margin: auto;">
        <div class="header">
            <table width="100%">
                <tr>
                    <td>
                        <h1>Hi, {{ $client->name }}</h1>
                        <p>Berikut adalah penawaran spesial untuk Anda.</p>
                    </td>
                    <td align="right">
                        <h2>{{ $number }}</h2>
                        <small>{{ $date }}</small>
                    </td>
                </tr>
            </table>
        </div>
        <div class="body">
            @foreach($items as $item)
            <div class="item-row">
                <span>{{ $item->description }} <small>(x{{ $item->quantity }})</small></span>
                <span>{{ format_currency($item->total) }}</span>
            </div>
            @endforeach
            
            <div class="total-row">
                GRAND TOTAL: {{ format_currency($total) }}
            </div>
            
            <p style="margin-top: 20px; font-size: 12px; color: #888;">
                Penawaran ini valid sampai {{ $valid_until }}. Kami menunggu kabar baik dari Anda!
            </p>
        </div>
    </div>
</body>
</html>
HTML
            ]
        );

        // --------------------------------------------------------------------------------
        // RECEIPTS (KWITANSI)
        // --------------------------------------------------------------------------------

        // 1. Official Receipt
        DocumentTemplate::updateOrCreate(
            ['name' => 'Official Receipt', 'type' => 'receipt', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/e2e8f0/475569?text=Official+Receipt',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt { border: 2px solid #000; padding: 20px; position: relative; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 100px; color: rgba(0,0,0,0.05); z-index: -1; }
        .header { text-align: center; border-bottom: 2px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .row { display: flex; margin-bottom: 10px; }
        .label { width: 150px; font-weight: bold; }
        .value { flex: 1; border-bottom: 1px dotted #000; }
        .amount { background: #eee; padding: 10px; font-weight: bold; font-size: 18px; display: inline-block; margin-top: 20px; border: 1px solid #000; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="watermark">LUNAS</div>
        <div class="header">
            <h2>KWITANSI PEMBAYARAN</h2>
            <p>No: {{ $number }}</p>
        </div>
        
        <div class="row">
            <span class="label">Telah Terima Dari:</span>
            <span class="value">{{ $client->name }}</span>
        </div>
        <div class="row">
            <span class="label">Uang Sejumlah:</span>
            <span class="value">{{ terbilang($amount) }} Rupiah</span>
        </div>
        <div class="row">
            <span class="label">Untuk Pembayaran:</span>
            <span class="value">{{ $description }}</span>
        </div>
        
        <table width="100%" style="margin-top: 30px;">
            <tr>
                <td valign="bottom"><div class="amount">{{ format_currency($amount) }}</div></td>
                <td align="right" valign="bottom">
                    <p>{{ $tenant->city }}, {{ $date }}</p>
                    <br><br><br>
                    <p>( {{ $tenant->company_name }} )</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML
                ,
                'is_default' => true
            ]
        );

        // 2. Simple Receipt
        DocumentTemplate::updateOrCreate(
            ['name' => 'Simple Receipt', 'type' => 'receipt', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/f1f5f9/475569?text=Simple+Receipt',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: monospace; }
        .box { border: 1px dashed #333; padding: 20px; max-width: 600px; margin: auto; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="text-align: center;">TANDA TERIMA</h2>
        <p>No: {{ $number }}</p>
        <hr>
        <p><strong>Diterima Dari:</strong> {{ $client->name }}</p>
        <p><strong>Sejumlah:</strong> {{ format_currency($amount) }}</p>
        <p><strong>Keterangan:</strong> {{ $description }}</p>
        <hr>
        <p style="text-align: right;">{{ $date }}<br><br>TTD,<br>{{ $tenant->company_name }}</p>
    </div>
</body>
</html>
HTML
            ]
        );

        // 3. Modern Receipt
        DocumentTemplate::updateOrCreate(
            ['name' => 'Modern Receipt', 'type' => 'receipt', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/0ea5e9/ffffff?text=Modern+Receipt',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; padding: 20px; }
        .card { background: white; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; max-width: 700px; margin: auto; }
        .header { background: #4a90e2; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .body { padding: 40px; }
        .field { margin-bottom: 20px; }
        .label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .value { font-size: 18px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .footer { background: #f1f3f5; padding: 20px; text-align: center; color: #888; font-size: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h3>PAYMENT RECEIPT</h3>
            <span>{{ $number }}</span>
        </div>
        <div class="body">
            <div class="field">
                <div class="label">Received From</div>
                <div class="value">{{ $client->name }}</div>
            </div>
            <div class="field">
                <div class="label">Amount</div>
                <div class="value" style="color: #4a90e2; font-weight: bold;">{{ format_currency($amount) }}</div>
            </div>
            <div class="field">
                <div class="label">For</div>
                <div class="value">{{ $description }}</div>
            </div>
            
            <table width="100%" style="margin-top: 40px;">
                <tr>
                    <td>
                        <div class="label">Payment Method</div>
                        <div>Transfer / Cash</div>
                    </td>
                    <td align="right">
                        <div class="label">Authorized Signature</div>
                        <br>
                        <strong>{{ $tenant->company_name }}</strong>
                    </td>
                </tr>
            </table>
        </div>
        <div class="footer">
            Thank you for your business!
        </div>
    </div>
</body>
</html>
HTML
            ]
        );

        // --------------------------------------------------------------------------------
        // DELIVERY NOTES (SURAT JALAN)
        // --------------------------------------------------------------------------------

        // 1. Standard Delivery Note
        DocumentTemplate::updateOrCreate(
            ['name' => 'Standard Delivery Note', 'type' => 'delivery_note', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/cbd5e1/475569?text=Standard+DO',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { border: 1px solid #ccc; padding: 20px; }
        .header center h1 { border-bottom: 2px solid #000; display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; }
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; }
        .sig-box { text-align: center; width: 200px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <center>
                <h1>SURAT JALAN</h1>
                <p>No: {{ $number }}</p>
            </center>
        </div>
        
        <table style="border: none; margin-bottom: 20px;">
            <tr style="border: none;">
                <td style="border: none;">
                    <strong>Pengirim:</strong><br>
                    {{ $tenant->company_name }}<br>
                    {{ $tenant->address }}
                </td>
                <td style="border: none; text-align: right;">
                    <strong>Penerima:</strong><br>
                    {{ $client->name }}<br>
                    {{ $client->address }}
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                <tr>
                    <td align="center">{{ $i+1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td align="center">{{ $item->quantity }}</td>
                    <td align="center">{{ $item->unit }}</td>
                    <td>{{ $item->notes }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table style="border: none; margin-top: 50px;">
            <tr style="border: none;">
                <td style="border: none; text-align: center;">
                    Penerima,
                    <br><br><br><br>
                    (....................)
                </td>
                <td style="border: none; text-align: center;">
                    Pengirim,
                    <br><br><br><br>
                    (....................)
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML
                ,
                'is_default' => true
            ]
        );

        // 2. Logistic Style
        DocumentTemplate::updateOrCreate(
            ['name' => 'Logistic Style', 'type' => 'delivery_note', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/475569/ffffff?text=Logistic+DO',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Courier New', Courier, monospace; }
        .wrap { border: 2px solid #000; padding: 15px; }
        .row { display: flex; justify-content: space-between; border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
        .items-list { width: 100%; text-align: left; }
        .items-list th { border-bottom: 2px solid #000; }
    </style>
</head>
<body>
    <div class="wrap">
        <h2>DELIVERY NOTE / SURAT JALAN</h2>
        <div class="row">
            <div>
                <strong>From:</strong> {{ $tenant->company_name }}
            </div>
            <div>
                <strong>Date:</strong> {{ $date }}<br>
                <strong>Doc No:</strong> {{ $number }}
            </div>
        </div>
        <div style="margin-bottom: 20px;">
            <strong>Ship To:</strong><br>
            {{ $client->name }}<br>
            {{ $client->address }}
        </div>
        
        <table class="items-list">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>QTY</th>
                    <th>UNIT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 40px;">
            <p><strong>Note:</strong> Barang telah diterima dengan baik dan cukup.</p>
        </div>
        
        <table width="100%" style="margin-top: 30px; text-align: center;">
            <tr>
                <td>Received By</td>
                <td>Driver</td>
                <td>Authorized By</td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML
            ]
        );

        // 3. Compact Delivery Note
        DocumentTemplate::updateOrCreate(
            ['name' => 'Compact Delivery Note', 'type' => 'delivery_note', 'is_system' => true],
            [
                'thumbnail' => 'https://placehold.co/400x300/f8fafc/94a3b8?text=Compact+DO',
                'html_content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { background: #333; color: white; padding: 10px; text-align: center; font-weight: bold; }
        .info { padding: 10px; display: flex; justify-content: space-between; background: #eee; }
        .list { width: 100%; border-collapse: collapse; }
        .list td, .list th { padding: 5px; border-bottom: 1px solid #ddd; }
        .checkstroke { width: 20px; height: 20px; border: 1px solid #000; display: inline-block; }
    </style>
</head>
<body>
    <div class="header">SURAT JALAN / {{ $number }}</div>
    <div class="info">
        <div>{{ $tenant->company_name }}</div>
        <div>{{ $date }}</div>
    </div>
    <div style="padding: 10px; border-bottom: 1px solid #ccc;">
        Tujuan: <strong>{{ $client->name }}</strong>
    </div>
    
    <table class="list">
        <thead>
            <tr>
                <th width="30">Check</th>
                <th>Item</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td align="center"><div class="checkstroke"></div></td>
                <td>{{ $item->product_name }}<br><small>{{ $item->notes }}</small></td>
                <td align="center">{{ $item->quantity }} {{ $item->unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: center;">
        <p>Silakan periksa barang sebelum tanda tangan.</p>
        <br><br>
        ( Tanda Tangan Penerima )
    </div>
</body>
</html>
HTML
            ]
        );
    }
}

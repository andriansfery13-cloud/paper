<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Template - {{ $template->name }}</title>
    <!-- Use Tailwind CDN for preview -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f3f4f6; padding: 20px; }
        .page {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        @media print {
            body { background: none; padding: 0; }
            .page { box-shadow: none; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- For preview, we'll replace placeholders with dummy data if needed, but since we are just previewing HTML, we render raw HTML first --}}
        {{-- In a real app, we'd pass a dummy invoice object and render it using string replacement --}}
        
        @php
            // Simple placeholder replacement for preview
            $content = $template->html_content;
            $placeholders = [
                'invoice_number' => 'INV/2026/0001',
                'date' => date('d M Y'),
                'due_date' => date('d M Y', strtotime('+30 days')),
                'company_name' => auth()->user()->tenant->company_name ?? 'Perusahaan Contoh',
                'client_name' => 'PT Pelanggan Sejahtera',
                'items_table' => '<table class="w-full border-collapse"><thead><tr class="border-b"><th class="py-2 text-left">Deskripsi</th><th class="py-2 text-right">Qty</th><th class="py-2 text-right">Harga</th><th class="py-2 text-right">Total</th></tr></thead><tbody><tr class="border-b"><td class="py-2">Jasa Web Development</td><td class="py-2 text-right">1</td><td class="py-2 text-right">5.000.000</td><td class="py-2 text-right">5.000.000</td></tr><tr class="border-b"><td class="py-2">Hosting 1 Tahun</td><td class="py-2 text-right">1</td><td class="py-2 text-right">1.500.000</td><td class="py-2 text-right">1.500.000</td></tr></tbody></table>',
                'subtotal' => '6.500.000',
                'tax' => '715.000',
                'total' => '7.215.000',
                'notes' => 'Terima kasih atas kepercayaan Anda',
                'terms' => 'Pembayaran ditransfer ke BCA 1234567890',
                'qr_code' => '<img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=Example" alt="QR">',
                'signature' => '<div style="height:50px; border-bottom:1px solid #ccc; width:200px; margin-top:20px;"></div>',
            ];

            foreach($placeholders as $key => $value) {
                // Handle Blade syntax escaping if any used in template, though we used @{{ }} in instruction
                // But typically users might just type {{ $var }} or @{{ var }} depending on logic.
                $content = str_replace(
                    ['{{ $' . $key . ' }}', '{{ ' . $key . ' }}', '{{' . $key . '}}', '{{$' . $key . '}}'],
                    $value,
                    $content
                );
            }
        @endphp

        {!! $content !!}
    </div>
</body>
</html>

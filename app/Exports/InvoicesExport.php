<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $tenantId;
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($tenantId, $startDate = null, $endDate = null, $status = null)
    {
        $this->tenantId = $tenantId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function query()
    {
        $query = Invoice::query()
            ->with('client')
            ->where('tenant_id', $this->tenantId);

        if ($this->startDate) {
            $query->whereDate('invoice_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('invoice_date', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('invoice_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'No. Invoice',
            'Tanggal',
            'Jatuh Tempo',
            'Client',
            'Subtotal',
            'Diskon',
            'Pajak',
            'Total',
            'Dibayar',
            'Sisa',
            'Status',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->invoice_date->format('d/m/Y'),
            $invoice->due_date->format('d/m/Y'),
            $invoice->client->name ?? '-',
            $invoice->subtotal,
            $invoice->discount_amount,
            $invoice->tax_amount,
            $invoice->total,
            $invoice->amount_paid,
            $invoice->amount_due,
            strtoupper($invoice->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6'],
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'],
                    'bold' => true,
                ],
            ],
        ];
    }
}

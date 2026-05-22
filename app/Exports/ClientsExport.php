<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClientsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $tenantId;

    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function query()
    {
        return Client::query()
            ->where('tenant_id', $this->tenantId)
            ->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama',
            'Email',
            'Telepon',
            'Alamat',
            'Kota',
            'Provinsi',
            'NPWP',
            'Term Pembayaran (Hari)',
            'Limit Kredit',
            'Total Piutang',
            'Status',
        ];
    }

    public function map($client): array
    {
        return [
            $client->client_code,
            $client->name,
            $client->email,
            $client->phone,
            $client->address,
            $client->city,
            $client->province,
            $client->npwp,
            $client->payment_term_days,
            $client->credit_limit,
            $client->outstanding_receivables,
            $client->is_active ? 'Active' : 'Inactive',
        ];
    }
}

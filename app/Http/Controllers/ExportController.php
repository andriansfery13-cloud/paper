<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Exports\ClientsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
    }

    /**
     * Export invoices to Excel
     */
    public function invoices(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Invoice::class);

        $tenantId = auth()->user()->tenant_id;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');

        $filename = 'invoices_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new InvoicesExport($tenantId, $startDate, $endDate, $status),
            $filename
        );
    }

    /**
     * Export clients to Excel
     */
    public function clients(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Client::class);

        $tenantId = auth()->user()->tenant_id;
        $filename = 'clients_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new ClientsExport($tenantId),
            $filename
        );
    }
}

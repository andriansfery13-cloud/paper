<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index(Request $request)
    {
        // Get clients with outstanding invoices
        $clients = Client::query()
            ->withSum([
                'invoices as total_outstanding' => function ($query) {
                    $query->whereIn('status', ['sent', 'viewed', 'partial', 'overdue']);
                }
            ], 'amount_due')
            ->having('total_outstanding', '>', 0)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('total_outstanding', 'desc')
            ->paginate(15);

        // Calculate totals
        $totalReceivables = Invoice::whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])
            ->sum('amount_due');

        $overdueReceivables = Invoice::where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('due_date', '<', now())
                    ->whereIn('status', ['sent', 'viewed', 'partial']);
            })
            ->sum('amount_due');

        return view('finance.receivables.index', compact('clients', 'totalReceivables', 'overdueReceivables'));
    }

    public function show(Client $client)
    {
        // Get all unpaid invoices for this client
        $invoices = Invoice::where('client_id', $client->id)
            ->whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Calculate aging buckets
        $aging = [
            'current' => 0,
            '1_30' => 0,
            '31_60' => 0,
            '61_90' => 0,
            'over_90' => 0,
        ];

        foreach ($invoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->due_date, false);

            if ($daysOverdue >= 0) {
                $aging['current'] += $invoice->amount_due;
            } elseif ($daysOverdue >= -30) {
                $aging['1_30'] += $invoice->amount_due;
            } elseif ($daysOverdue >= -60) {
                $aging['31_60'] += $invoice->amount_due;
            } elseif ($daysOverdue >= -90) {
                $aging['61_90'] += $invoice->amount_due;
            } else {
                $aging['over_90'] += $invoice->amount_due;
            }
        }

        $totalOutstanding = array_sum($aging);

        return view('finance.receivables.show', compact('client', 'invoices', 'aging', 'totalOutstanding'));
    }
}

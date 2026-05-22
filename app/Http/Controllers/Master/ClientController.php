<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
        $this->middleware('permission:clients.view')->only(['index', 'show']);
        $this->middleware('permission:clients.create')->only(['create', 'store']);
        $this->middleware('permission:clients.edit')->only(['edit', 'update']);
        $this->middleware('permission:clients.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Client::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortDir = $request->get('dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $clients = $query->paginate(15)->withQueryString();

        return view('master.clients.index', compact('clients'));
    }

    public function create()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->canCreateClient()) {
            $quota = $tenant->getQuotaStatus();
            return redirect()->route('clients.index')
                ->with('quota_exceeded', true)
                ->with('quota_type', 'client')
                ->with('quota_message', 'Kuota client telah habis. Upgrade paket untuk menambah client baru.')
                ->with('quota_usage', json_encode($quota));
        }
        return view('master.clients.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->canCreateClient()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'quota_exceeded' => true,
                    'quota_type' => 'client',
                    'message' => 'Kuota client telah habis.',
                    'usage' => $tenant->getQuotaStatus()
                ], 403);
            }
            return redirect()->route('clients.index')
                ->with('quota_exceeded', true)
                ->with('quota_type', 'client')
                ->with('quota_message', 'Kuota client telah habis. Upgrade paket untuk menambah client baru.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'npwp' => 'nullable|string|max:30',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'payment_term_days' => 'nullable|integer|min:0|max:365',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        $client = Client::create($validated);

        if ($request->wantsJson()) {
            return $this->successResponse($client, 'Client berhasil ditambahkan', 201);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil ditambahkan');
    }

    public function show(Client $client)
    {
        $client->load([
            'invoices' => function ($q) {
                $q->latest()->take(10);
            },
            'quotations' => function ($q) {
                $q->latest()->take(10);
            }
        ]);

        return view('master.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('master.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'npwp' => 'nullable|string|max:30',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'payment_term_days' => 'nullable|integer|min:0|max:365',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $client->update($validated);

        if ($request->wantsJson()) {
            return $this->successResponse($client, 'Client berhasil diperbarui');
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil diperbarui');
    }

    public function destroy(Client $client)
    {
        // Check if client has invoices
        if ($client->invoices()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus client yang memiliki invoice');
        }

        $client->delete();

        if (request()->wantsJson()) {
            return $this->successResponse(null, 'Client berhasil dihapus');
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil dihapus');
    }

    // API: Get clients for select dropdown
    public function select(Request $request)
    {
        $query = Client::active();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $clients = $query->select('id', 'code', 'name', 'email', 'payment_term_days')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($clients);
    }
}

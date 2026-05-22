<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Invoices",
 *     description="API Endpoints for invoice management"
 * )
 */
class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'log.activity']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/invoices",
     *     summary="Get all invoices",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="client_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of invoices")
     * )
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'items'])
            ->where('tenant_id', $request->user()->tenant_id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $invoices = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/invoices",
     *     summary="Create new invoice",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_id","items"},
     *             @OA\Property(property="client_id", type="integer"),
     *             @OA\Property(property="due_date", type="string", format="date"),
     *             @OA\Property(property="subject", type="string"),
     *             @OA\Property(property="notes", type="string"),
     *             @OA\Property(property="items", type="array", @OA\Items(
     *                 @OA\Property(property="product_id", type="integer"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="quantity", type="number"),
     *                 @OA\Property(property="unit_price", type="number")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Invoice created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'subject' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'discount_type' => 'nullable|integer|in:0,1',
            'discount_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = $request->user();

            $invoice = Invoice::create([
                'tenant_id' => $user->tenant_id,
                'client_id' => $request->client_id,
                'created_by' => $user->id,
                'invoice_date' => $request->invoice_date ?? now(),
                'due_date' => $request->due_date ?? now()->addDays(30),
                'subject' => $request->subject,
                'notes' => $request->notes,
                'terms' => $request->terms,
                'discount_type' => $request->discount_type ?? 0,
                'discount_value' => $request->discount_value ?? 0,
                'status' => 'draft',
            ]);

            foreach ($request->items as $index => $item) {
                $taxPercent = $item['tax_percent'] ?? 0;
                $subtotal = $item['quantity'] * $item['unit_price'];
                $taxAmount = $subtotal * ($taxPercent / 100);

                $invoice->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'pcs',
                    'unit_price' => $item['unit_price'],
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'subtotal' => $subtotal,
                    'sort_order' => $index,
                ]);
            }

            $invoice->calculateTotals();
            $invoice->save();

            DB::commit();

            $invoice->load(['client', 'items']);

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/invoices/{id}",
     *     summary="Get invoice by ID",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Invoice data"),
     *     @OA\Response(response=404, description="Invoice not found")
     * )
     */
    public function show(Request $request, $id)
    {
        $invoice = Invoice::with(['client', 'items.product', 'payments'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $invoice,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/invoices/{id}/send",
     *     summary="Send invoice to client",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Invoice sent"),
     *     @OA\Response(response=404, description="Invoice not found")
     * )
     */
    public function send(Request $request, $id)
    {
        $invoice = Invoice::where('tenant_id', $request->user()->tenant_id)
            ->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        $invoice->markAsSent();

        return response()->json([
            'success' => true,
            'message' => 'Invoice sent successfully',
            'data' => $invoice,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/invoices/{id}",
     *     summary="Delete invoice",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Invoice deleted"),
     *     @OA\Response(response=404, description="Invoice not found")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $invoice = Invoice::where('tenant_id', $request->user()->tenant_id)
            ->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        if (!$invoice->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice cannot be deleted in current status',
            ], 400);
        }

        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Clients",
 *     description="API Endpoints for client management"
 * )
 */
class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'log.activity']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/clients",
     *     summary="Get all clients",
     *     tags={"Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", description="Search term", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="List of clients",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Client::where('tenant_id', $request->user()->tenant_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('name')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $clients,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/clients",
     *     summary="Create new client",
     *     tags={"Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="company", type="string"),
     *             @OA\Property(property="address", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Client created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:50',
            'payment_term_days' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $client = Client::create([
            'tenant_id' => $request->user()->tenant_id,
            ...$validator->validated(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client created successfully',
            'data' => $client,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/clients/{id}",
     *     summary="Get client by ID",
     *     tags={"Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Client data"),
     *     @OA\Response(response=404, description="Client not found")
     * )
     */
    public function show(Request $request, $id)
    {
        $client = Client::where('tenant_id', $request->user()->tenant_id)
            ->find($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $client,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/clients/{id}",
     *     summary="Update client",
     *     tags={"Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="company", type="string"),
     *             @OA\Property(property="address", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Client updated"),
     *     @OA\Response(response=404, description="Client not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $client = Client::where('tenant_id', $request->user()->tenant_id)
            ->find($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:50',
            'payment_term_days' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $client->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully',
            'data' => $client,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/clients/{id}",
     *     summary="Delete client",
     *     tags={"Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Client deleted"),
     *     @OA\Response(response=404, description="Client not found")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $client = Client::where('tenant_id', $request->user()->tenant_id)
            ->find($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found',
            ], 404);
        }

        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully',
        ]);
    }
}

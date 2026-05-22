<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
        $this->middleware('permission:products.view')->only(['index', 'show']);
        $this->middleware('permission:products.create')->only(['create', 'store']);
        $this->middleware('permission:products.edit')->only(['edit', 'update']);
        $this->middleware('permission:products.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by stock
        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->lowStock();
            } elseif ($request->stock === 'out') {
                $query->where('stock', '<=', 0)->where('track_stock', true);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortDir = $request->get('dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $products = $query->paginate(15)->withQueryString();
        $categories = ProductCategory::active()->orderBy('name')->get();

        return view('master.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->canCreateProduct()) {
            $quota = $tenant->getQuotaStatus();
            return redirect()->route('products.index')
                ->with('quota_exceeded', true)
                ->with('quota_type', 'product')
                ->with('quota_message', 'Kuota produk telah habis. Upgrade paket untuk menambah produk baru.')
                ->with('quota_usage', json_encode($quota));
        }
        $categories = ProductCategory::active()->orderBy('name')->get();
        return view('master.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->canCreateProduct()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'quota_exceeded' => true,
                    'quota_type' => 'product',
                    'message' => 'Kuota produk telah habis.',
                    'usage' => $tenant->getQuotaStatus()
                ], 403);
            }
            return redirect()->route('products.index')
                ->with('quota_exceeded', true)
                ->with('quota_type', 'product')
                ->with('quota_message', 'Kuota produk telah habis. Upgrade paket untuk menambah produk baru.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
            'track_stock' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        if ($request->wantsJson()) {
            return $this->successResponse($product, 'Produk berhasil ditambahkan', 201);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('master.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::active()->orderBy('name')->get();
        return view('master.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
            'track_stock' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        if ($request->wantsJson()) {
            return $this->successResponse($product, 'Produk berhasil diperbarui');
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        // Check if product is used in invoices
        if ($product->invoiceItems()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus produk yang digunakan dalam invoice');
        }

        // Delete image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        if (request()->wantsJson()) {
            return $this->successResponse(null, 'Produk berhasil dihapus');
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    // API: Get products for select dropdown
    public function select(Request $request)
    {
        $query = Product::active();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $products = $query->select('id', 'code', 'name', 'unit', 'selling_price', 'tax_rate', 'is_taxable')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($products);
    }
}

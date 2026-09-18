<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display products with search, price filters,
     * sorting and pagination.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'min_price' => 'nullable|integer|min:1',
            'max_price' => 'nullable|integer|min:1',
            'sort_by' => 'nullable|in:id,name,price,created_at,updated_at',
            'sort_direction' => 'nullable|in:asc,asc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Product::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if (!empty($validated['search'])) {
            $search = $validated['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Minimum Price
        |--------------------------------------------------------------------------
        */
        if (isset($validated['min_price'])) {
            $query->where('price', '>=', $validated['min_price']);
        }

        /*
        |--------------------------------------------------------------------------
        | Maximum Price
        |--------------------------------------------------------------------------
        */
        if (isset($validated['max_price'])) {
            $query->where('price', '<=', $validated['max_price']);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDirection = $validated['sort_direction'] ?? 'asc';

        $query->orderBy($sortBy, $sortDirection);

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $perPage = $validated['per_page'] ?? 10;

        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'filters' => [
                'search' => $validated['search'] ?? null,
                'min_price' => $validated['min_price'] ?? null,
                'max_price' => $validated['max_price'] ?? null,
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection,
                'per_page' => $perPage,
            ],
            'data' => $products,
        ], 200);
    }

    /**
     * Display a single product.
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => $product,
        ], 200);
    }

    /**
     * Store a new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|unique:products,name',
            'price' => 'required|integer|min:1',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|unique:products,name,' . $product->id,
            'price' => 'required|integer|min:1',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->fresh(),
        ], 200);
    }

    /**
     * Delete an existing product.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ], 200);
    }

    /**
     * Bulk delete products.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:products,id',
        ]);

        $deletedCount = Product::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Products deleted successfully',
            'deleted_count' => $deletedCount,
        ], 200);
    }

    /**
     * Product statistics.
     */
    public function statistics()
    {
        $totalProducts = Product::count();
        $totalValue = Product::sum('price');
        $averagePrice = Product::avg('price');
        $minimumPrice = Product::min('price');
        $maximumPrice = Product::max('price');

        return response()->json([
            'success' => true,
            'message' => 'Product statistics retrieved successfully',
            'data' => [
                'total_products' => $totalProducts,
                'total_value' => (int) $totalValue,
                'average_price' => round((float) ($averagePrice ?? 0), 2),
                'minimum_price' => (int) ($minimumPrice ?? 0),
                'maximum_price' => (int) ($maximumPrice ?? 0),
            ],
        ], 200);
    }
}
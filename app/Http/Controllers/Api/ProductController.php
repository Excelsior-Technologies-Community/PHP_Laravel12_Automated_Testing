<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display all products.
     */
    public function index()
    {
        return response()->json(Product::all(), 200);
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

        return response()->json($product, 201);
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
            'message' => 'Product updated successfully',
            'product' => $product->fresh(),
        ], 200);
    }

    /**
     * Delete an existing product.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
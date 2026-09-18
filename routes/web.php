<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Create Product
|--------------------------------------------------------------------------
*/

Route::get('/product/create', function () {
    return view('product-form');
});

/*
|--------------------------------------------------------------------------
| Store Product
|--------------------------------------------------------------------------
*/

Route::post('/product/store', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|min:3|unique:products,name',
        'price' => 'required|integer|min:1',
    ]);

    Product::create($validated);

    return redirect('/product/create')
        ->with('success', 'Product Added Successfully');
});

/*
|--------------------------------------------------------------------------
| Product List
|--------------------------------------------------------------------------
|
| Supported:
|
| /products
| /products?search=laptop
| /products?min_price=1000
| /products?max_price=50000
| /products?sort_by=price
| /products?sort_direction=asc
|
*/

Route::get('/products', function (Request $request) {

    $query = Product::query();

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {

        $search = $request->search;

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

    if ($request->filled('min_price')) {
        $query->where(
            'price',
            '>=',
            $request->integer('min_price')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Maximum Price
    |--------------------------------------------------------------------------
    */

    if ($request->filled('max_price')) {
        $query->where(
            'price',
            '<=',
            $request->integer('max_price')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    $allowedSortColumns = [
        'id',
        'name',
        'price',
        'created_at',
        'updated_at',
    ];

    $sortBy = $request->get('sort_by', 'id');

    if (!in_array($sortBy, $allowedSortColumns)) {
        $sortBy = 'id';
    }

    $sortDirection = $request->get('sort_direction', 'desc');

    if (!in_array($sortDirection, ['asc', 'desc'])) {
        $sortDirection = 'desc';
    }

    $query->orderBy($sortBy, $sortDirection);

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    $products = $query
        ->paginate(10)
        ->withQueryString();

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    $totalProducts = Product::count();

    $totalValue = Product::sum('price');

    $averagePrice = Product::avg('price');

    $minimumPrice = Product::min('price');

    $maximumPrice = Product::max('price');

    return view('products', compact(
        'products',
        'totalProducts',
        'totalValue',
        'averagePrice',
        'minimumPrice',
        'maximumPrice',
        'sortBy',
        'sortDirection'
    ));
});

/*
|--------------------------------------------------------------------------
| Delete Product From Web
|--------------------------------------------------------------------------
*/

Route::delete('/product/{product}', function (Product $product) {

    $product->delete();

    return redirect('/products')
        ->with('success', 'Product deleted successfully');
});
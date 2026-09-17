<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/product/create', function () {
    return view('product-form');
});

Route::post('/product/store', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|min:3|unique:products,name',
        'price' => 'required|integer|min:1',
    ]);

    Product::create($validated);

    return redirect('/product/create')
        ->with('success', 'Product Added');
});

Route::get('/products', function () {
    $products = Product::latest()->get();

    return view('products', compact('products'));
});
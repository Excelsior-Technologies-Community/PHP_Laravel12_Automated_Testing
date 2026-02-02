<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/product/create', function () {
    return view('product-form');
});

Route::post('/product/store', function (Request $request) {
    Product::create($request->validate([
        'name' => 'required',
        'price' => 'required|integer'
    ]));

    return redirect('/product/create')->with('success', 'Product Added');
});

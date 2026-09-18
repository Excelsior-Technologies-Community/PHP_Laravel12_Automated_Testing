<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Product API Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Product Statistics
|--------------------------------------------------------------------------
*/
Route::get('/products/statistics', [
    ProductController::class,
    'statistics'
]);

/*
|--------------------------------------------------------------------------
| Bulk Delete
|--------------------------------------------------------------------------
*/
Route::post('/products/bulk-delete', [
    ProductController::class,
    'bulkDestroy'
]);

/*
|--------------------------------------------------------------------------
| Product List
|--------------------------------------------------------------------------
|
| Supports:
| ?search=laptop
| ?min_price=1000
| ?max_price=50000
| ?sort_by=price
| ?sort_direction=asc
| ?per_page=10
|
*/
Route::get('/products', [
    ProductController::class,
    'index'
]);

/*
|--------------------------------------------------------------------------
| Single Product
|--------------------------------------------------------------------------
*/
Route::get('/products/{product}', [
    ProductController::class,
    'show'
]);

/*
|--------------------------------------------------------------------------
| Create Product
|--------------------------------------------------------------------------
*/
Route::post('/products', [
    ProductController::class,
    'store'
]);

/*
|--------------------------------------------------------------------------
| Update Product
|--------------------------------------------------------------------------
*/
Route::put('/products/{product}', [
    ProductController::class,
    'update'
]);

/*
|--------------------------------------------------------------------------
| Delete Product
|--------------------------------------------------------------------------
*/
Route::delete('/products/{product}', [
    ProductController::class,
    'destroy'
]);
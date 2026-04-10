<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('getPrice', [SaleController::class, 'getPrice']);
Route::get('getSales/{station}', [SaleController::class, 'getSales']);
Route::get('getProducts', [SaleController::class, 'getProducts']);
Route::post('updateSale', [SaleController::class, 'update']);
Route::get('requests', [SaleController::class, 'requests']);
Route::post('syncProducts', [SaleController::class, 'syncProducts']);
Route::post('syncRequest', [SaleController::class, 'syncRequest']);

// Products API
Route::get('products', [ProductController::class, 'index']);
Route::get('products/categories', [ProductController::class, 'categories']);
Route::get('products/{id}', [ProductController::class, 'show']);

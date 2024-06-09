<?php

use App\Http\Controllers\Api\AddToCartController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProductCustomerController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerMiddleware;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['middleware' => 'auth:sanctum'], function(){
    // Logout
    Route::post('logout',[AuthController::class, 'logout']);
});

Route::post('register',[AuthController::class, 'register']);
Route::post('login',[AuthController::class, 'login']);


Route::group(['middleware' => ['auth:sanctum', AdminMiddleware::class], 'prefix' => 'admin'], function() {
    Route::apiResource('categories', CategoryController::class);
    
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

Route::group(['middleware' => ['auth:sanctum', CustomerMiddleware::class]], function(){
    Route::post('/add-to-cart', [AddToCartController::class, 'store']);
    Route::get('/add-to-cart', [AddToCartController::class, 'index']);
    Route::patch('/add-to-cart/{id}', [AddToCartController::class, 'update']);
    Route::delete('/add-to-cart/{id}', [AddToCartController::class, 'destroy']);

    Route::get('/products', [ProductCustomerController::class, 'index']);
    Route::get('/products{id}', [ProductCustomerController::class, 'show']);



    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'store']);
}); 



Route::apiResource('products', ProductController::class);
Route::apiResource('carts', AddToCartController::class);
Route::apiResource('checkouts', CheckoutController::class);
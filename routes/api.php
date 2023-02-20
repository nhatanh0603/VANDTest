<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/users', [AuthController::class, 'index']);

Route::controller(AuthController::class)->group(function() {
    Route::post('signup', 'signup');
    Route::post('signin', 'signin');
    Route::delete('signout', 'signout')->middleware('auth:sanctum');
});


Route::prefix('stores')->middleware('auth:sanctum')->controller(StoreController::class)->group(function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::get('/search/{keyword}', 'search');
    Route::post('/', 'store');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::prefix('products')->middleware('auth:sanctum')->controller(ProductController::class)->group(function() {
    Route::get('/{store_id}', 'index');
    Route::get('/{store_id}/{id}', 'show');
    Route::get('/{store_id}/search/{keyword}', 'search');
    Route::post('/', 'store');
    Route::put('/{id}', 'update');
    Route::delete('/{store_id}/{id}', 'destroy');
});

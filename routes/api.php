<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/', fn(Request $request) => $request->guard('admin')->user());
});

Route::apiResource('categories', \App\Http\Controllers\CategoryController::class);

Route::apiResource('brands', \App\Http\Controllers\BrandController::class);

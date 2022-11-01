<?php

use App\Http\Controllers\Api\AccessTokensController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\UsersController;
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

// Api
Route::middleware('auth:sanctum')
    ->group(function () {

        // Tokens
        Route::post('auth/tokens', [AccessTokensController::class, 'store'])->withoutMiddleware('auth:sanctum');
        Route::post('auth/register', [AccessTokensController::class, 'register'])->withoutMiddleware('auth:sanctum');
        Route::delete('auth/tokens', [AccessTokensController::class, 'destroy']);

        // Auth User
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Users
        Route::controller(UsersController::class)
        ->middleware('auth:sanctum')
        ->group(function () {
            Route::put('/users/trash/{user?}', 'restore');
            Route::delete('/users/trash/{user?}', 'forceDelete');
            Route::apiResource('/users', UsersController::class);
        });

        // categories
        Route::controller(CategoriesController::class)
        ->middleware('auth:sanctum')
        ->group(function () {
            Route::put('/categories/trash/{category?}', 'restore');
            Route::delete('/categories/trash/{category?}', 'forceDelete');
            // Route::post('/categories/status/{category}', 'changeStatus');
            Route::apiResource('/categories', CategoriesController::class);
        });

            // products
        Route::controller(ProductsController::class)
        ->middleware('auth:sanctum')
        ->group(function () {
            Route::put('/products/trash/{product?}', 'restore');
            Route::delete('/products/trash/{product?}', 'forceDelete');
            // Route::post('/products/status/{category}', 'changeStatus');
            Route::apiResource('/products', ProductsController::class);
        });

        
    });
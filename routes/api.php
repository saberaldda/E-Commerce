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
        Route::post('auth/tokens/delete', [AccessTokensController::class, 'destroy']);

        // Auth User
        Route::get('/currentuser', function (Request $request) {
            return $request->user();
        });

        // Users
        Route::controller(UsersController::class)
        ->middleware('auth:sanctum')
        ->group(function () {
            // Route::put('/users/trash/{user?}', 'restore');
            // Route::delete('/users/trash/{user?}', 'forceDelete');
            // Route::apiResource('/users', UsersController::class);
            Route::get('users', 'index')->name('users.index');
            Route::post('users/store', 'store')->name('users.store');
            Route::get('users/{user}', 'show')->name('users.show');
            Route::post('users/{user}/update', 'update')->name('users.update');
            Route::post('users/{user}/delete', 'destroy')->name('users.destroy');
        });

        // categories
        Route::controller(CategoriesController::class)
        ->middleware('auth:sanctum')
        ->group(function () {
            // Route::put('/categories/trash/{category?}', 'restore');
            // Route::delete('/categories/trash/{category?}', 'forceDelete');
            Route::post('/categories/status/{category}', 'changeStatus');
            // Route::apiResource('/categories', CategoriesController::class);
            Route::get('categories', 'index')->name('categories.index');
            Route::post('categories/store', 'store')->name('categories.store');
            Route::get('categories/{user}', 'show')->name('categories.show');
            Route::post('categories/{user}/update', 'update')->name('categories.update');
            Route::post('categories/{user}/delete', 'destroy')->name('categories.destroy');
        });

            // products
        Route::controller(ProductsController::class)
        ->middleware('auth:sanctum')
        ->group(function () {
            // Route::put('/products/trash/{product?}', 'restore');
            // Route::delete('/products/trash/{product?}', 'forceDelete');
            Route::post('/products/status/{product}', 'changeStatus');
            // Route::apiResource('/products', ProductsController::class);
            Route::get('products', 'index')->name('products.index');
            Route::post('products/store', 'store')->name('products.store');
            Route::get('products/{user}', 'show')->name('products.show');
            Route::post('products/{user}/update', 'update')->name('products.update');
            Route::post('products/{user}/delete', 'destroy')->name('products.destroy');
        });

    });
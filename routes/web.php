<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CurrencyConverterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function()
{
    return view('welcome');
});

// Admin
Route::prefix('admin')
    ->middleware(['auth', 'auth.type:superadmin,admin'])
    ->group(function () {

            // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Currency Converter
        Route::post('currency', [CurrencyConverterController::class, 'store'])->name('currency.store');

            // Profile
        Route::controller(ProfileController::class)->middleware('auth')
        ->group(function () {
            Route::get('profile/{user}', 'show')->name('profile.show');
            Route::post('profile/update/{user}', 'update')->name('profile.update');
            Route::post('profile/changepass/{user}', 'changePass')->name('profile.change-pass');
        });

            // contact-us
        Route::controller(ContactController::class)
            ->withoutMiddleware(['auth.type:admin'])
            ->group(function () {
                Route::get('/contact-us', 'index')->name('contact.index');
                Route::post('/contact-us', 'store')->name('contact.store');
        });

            // categories
        Route::controller(CategoriesController::class)
            ->group(function () {
                // Route::get('/categories/trash', 'trash')->name('categories.trash');
                // Route::put('/categories/trash/{category?}', 'restore')->name('categories.restore');
                // Route::delete('/categories/trash/{category?}', 'forceDelete')->name('categories.force-delete');
                Route::post('/categories/status/{category}', 'changeStatus')->name('categories.change-status');
                Route::get('/categories/cascadedelete/{category}', 'deleteWithProducts')->name('categories.delete-with-products');
                // Route::resource('/categories', CategoriesController::class);
                Route::get('categories', 'index')->name('categories.index');
                Route::get('categories/create', 'create')->name('categories.create');
                Route::post('categories/store', 'store')->name('categories.store');
                Route::get('categories/{category}', 'show')->name('categories.show');
                Route::get('categories/{category}/edit', 'edit')->name('categories.edit');
                Route::post('categories/{category}/update', 'update')->name('categories.update');
                Route::post('categories/{category}/delete', 'destroy')->name('categories.destroy');
            });

            // prodcuts
        Route::controller(ProductsController::class)
            ->group(function () {
                Route::get('/products/trash', 'trash')->name('products.trash');
                // Route::put('/products/trash/{product?}', 'restore')->name('products.restore');
                // Route::delete('/products/trash/{product?}', 'forceDelete')->name('products.force-delete');
                Route::post('/products/status/{product}', 'changeStatus')->name('products.change-status');
                // Route::resource('/products', ProductsController::class);
                Route::get('products', 'index')->name('products.index');
                Route::get('products/create', 'create')->name('products.create');
                Route::post('products/store', 'store')->name('products.store');
                Route::get('products/{product}', 'show')->name('products.show');
                Route::get('products/{product}/edit', 'edit')->name('products.edit');
                Route::post('products/{product}/update', 'update')->name('products.update');
                Route::post('products/{product}/delete', 'destroy')->name('products.destroy');
            });

            // Users Managment
        Route::controller(UsersController::class)
            ->group(function () {
                // Route::get('/users/trash', 'trash')->name('users.trash');
                // Route::put('/users/trash/{product?}', 'restore')->name('users.restore');
                // Route::delete('/users/trash/{product?}', 'forceDelete')->name('users.force-delete');
                // Route::resource('/users', UsersController::class);
                Route::get('users', 'index')->name('users.index');
                Route::get('users/create', 'create')->name('users.create');
                Route::post('users/store', 'store')->name('users.store');
                Route::get('users/{user}', 'show')->name('users.show');
                Route::get('users/{user}/edit', 'edit')->name('users.edit');
                Route::post('users/{user}/update', 'update')->name('users.update');
                Route::post('users/{user}/delete', 'destroy')->name('users.destroy');
            });

            // Countries
        // Route::controller(CountriesController::class)
        //     ->group(function () {
        //         Route::resource('/countries', CountriesController::class)->except(['show','edit','update']);
        //     });

            // Ratings
        // Route::controller(RatingsController::class)
        // ->group(function () {
        //     Route::get('ratings/','index')->name('ratings.index');
        //     Route::get('ratings/{type}/create','create')->where('type', 'product|profile')->name('ratings.create');
        //     Route::post('ratings/{type}','store')->where('type', 'product|profile')->name('ratings.store');
        // });
        
        //     // Notification
        // Route::controller(NotificationsController::class)
        // ->group(function () {
        //     Route::get('notifications', 'index')->name('notifications');
        //     Route::get('notifications/{id}', 'show')->name('notifications.read');
        //     Route::delete('notifications/{id}', 'delete')->name('notifications.delete');
        // });
        
        //     // Orders
        // Route::controller(OrdersController::class)
        // ->group(function () {
        //     Route::post('/orders/status/{order}', 'changeStatus')->name('orders.change-status');
        //     Route::resource('/orders', OrdersController::class)->except('store','update');
        // });

    });

    
require __DIR__.'/auth.php';
// require __DIR__.'/dashboard.php';

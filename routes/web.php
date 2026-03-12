<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [ItemController::class, 'getItems'])->name('getItems');
Route::get('/items/{items}', [ItemController::class, 'show'])->name('items.show');
Route::get('/add-to-cart/{id}', [ItemController::class, 'addToCart'])->name('addToCart');
Route::get('/shopping-cart', [ItemController::class, 'getCart'])->name('getCart');
Route::get('/reduce/{id}', [ItemController::class, 'getReduceByOne'])->name('reduceByOne');
Route::get('/remove/{id}', [ItemController::class, 'getRemoveItem'])->name('removeItem');
Route::get('/checkout', [ItemController::class, 'postCheckout'])->name('checkout');

Route::post('/items-import', [ItemController::class, 'import'])->name('item.import');
Route::post('/services-import', [ServiceController::class, 'import'])->name('service.import');

Route::middleware(['auth'])->group(function () {
    Route::post('/user/update/{id}', [UserController::class, 'update_role'])->name('users.update');
    Route::get('/shopping-cart', [ItemController::class, 'getCart'])->name('getCart');
    Route::get('/checkout', [ItemController::class, 'postCheckout'])->name('checkout');
    Route::post('/user/logout', [UserController::class, 'logout'])->name('user.logout');
    Route::resource('customers', CustomerController::class)->only(['create', 'store', 'show']);
    
    // Admin-only routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/customers', [DashboardController::class, 'getCustomers'])->name('admin.customers');
        Route::get('/users', [DashboardController::class, 'getUsers'])->name('admin.users');
        Route::get('/orders', [DashboardController::class, 'getOrders'])->name('admin.orders');
        Route::get('/order/{id}', [OrderController::class, 'processOrder'])->name('admin.orderDetails');
        Route::post('/order/{id}', [OrderController::class, 'orderUpdate'])->name('admin.orderUpdate');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        
        // Admin item management routes
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{items}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{items}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{items}', [ItemController::class, 'destroy'])->name('items.destroy');
        Route::patch('/items/{items}/restore', [ItemController::class, 'restore'])->name('items.restore');

        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::patch('/services/{service}/restore', [ServiceController::class, 'restore'])->name('services.restore');
    });
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

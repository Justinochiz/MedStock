<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReviewController;
use App\Models\User;
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
Route::get('/', function () {
    $user = Auth::user();

    if ($user instanceof User && $user->hasVerifiedEmail()) {
        $role = (string) $user->getAttribute('role');
        if ($role === 'admin') {
            return redirect()->route('dashboard.index');
        }

        return redirect()->route('getItems');
    }

    return redirect()->route('getItems');
})->name('landing');

Route::get('/shop', [ItemController::class, 'getItems'])->name('getItems');
Route::get('/shop/services', [ServiceController::class, 'shop'])->name('shop.services');
Route::get('/shop/services/{service}', [ServiceController::class, 'showPublic'])->name('shop.services.show');
Route::get('/items/{items}', [ItemController::class, 'show'])->name('items.show');
Route::get('/add-to-cart/{id}', [ItemController::class, 'addToCart'])->name('addToCart');
Route::get('/buy-now/{id}', [ItemController::class, 'buyNow'])->name('buyNow');
Route::get('/shopping-cart', [ItemController::class, 'getCart'])->name('getCart');
Route::get('/reduce/{id}', [ItemController::class, 'getReduceByOne'])->name('reduceByOne');
Route::get('/remove/{id}', [ItemController::class, 'getRemoveItem'])->name('removeItem');

Route::post('/items-import', [ItemController::class, 'import'])->name('item.import');
Route::post('/services-import', [ServiceController::class, 'import'])->name('service.import');

Route::get('/about-us', function () {
    return view('footer.info', [
        'pageTitle' => 'About MedStock',
        'subtitle' => 'Who we are and what we value',
        'items' => [
            'MedStock is built to help users manage medical inventory with clarity and speed.',
            'We prioritize policy and privacy for all users.',
            'Owner: Justine Tomon',
            'Owner: Shyr Nicole Belenzo',
            'Section: BSIT-S-3A',
        ],
    ]);
})->name('footer.about');

Route::get('/features', function () {
    return view('footer.info', [
        'pageTitle' => 'MedStock Features',
        'subtitle' => 'Core features of the system',
        'items' => [
            'Inventory tracking for medical supplies and equipment.',
            'Order management for faster processing and status updates.',
            'Service and product records in one platform.',
            'Admin dashboard analytics for monitoring operations.',
        ],
    ]);
})->name('footer.features');

Route::get('/contact-us', function () {
    return view('footer.info', [
        'pageTitle' => 'Contact Us',
        'subtitle' => 'Reach our team for more information',
        'items' => [
            'Facebook: Justine Tomon',
            'Facebook: Shyr Nicole Belenzo',
            'Email: tomonjustine74@gmail.com',
        ],
    ]);
})->name('footer.contact');

Route::get('/support', function () {
    return view('footer.info', [
        'pageTitle' => 'Support',
        'subtitle' => 'Need help with MedStock?',
        'items' => [
            'For support concerns, please message us on Facebook.',
            'If you have account or order issues, include your full name and issue details.',
            'You can also send concerns to tomonjustine74@gmail.com.',
        ],
    ]);
})->name('footer.support');

Route::get('/privacy-policy', function () {
    return view('footer.info', [
        'pageTitle' => 'Privacy Policy',
        'subtitle' => 'Your data and privacy',
        'items' => [
            'MedStock collects only data needed to provide core platform functions.',
            'User data is used for account access, order processing, and service management.',
            'We do not intentionally share personal information with unauthorized parties.',
            'For privacy concerns, contact tomonjustine74@gmail.com.',
        ],
    ]);
})->name('footer.privacy');

Route::get('/terms-of-service', function () {
    return view('footer.info', [
        'pageTitle' => 'Terms of Service',
        'subtitle' => 'Guidelines for using MedStock',
        'items' => [
            'Use MedStock responsibly and provide accurate account information.',
            'Unauthorized access attempts and misuse of data are prohibited.',
            'System features may be improved or updated as needed.',
            'By using MedStock, you agree to these service terms.',
        ],
    ]);
})->name('footer.terms');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/user/update/{id}', [UserController::class, 'update_role'])->name('users.update');
    Route::get('/profile', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/items/{item}/reviews', [ReviewController::class, 'storeItem'])->name('items.reviews.store');
    Route::post('/services/{service}/buy-now', [ServiceController::class, 'buyNow'])->name('services.buyNow');
    Route::post('/services/{service}/add-to-cart', [ServiceController::class, 'addToCart'])->name('services.addToCart');
    Route::get('/services/cart', [ServiceController::class, 'getCart'])->name('services.cart');
    Route::get('/services/cart/reduce/{service}', [ServiceController::class, 'getReduceByOne'])->name('services.reduceByOne');
    Route::get('/services/cart/remove/{service}', [ServiceController::class, 'getRemoveItem'])->name('services.removeItem');
    Route::get('/services/checkout', [ServiceController::class, 'showCartCheckout'])->name('services.checkout.cart');
    Route::post('/services/checkout', [ServiceController::class, 'postCartCheckout'])->name('services.checkout.cart.process');
    Route::get('/services/{service}/checkout', [ServiceController::class, 'showCheckout'])->name('services.checkout');
    Route::post('/services/{service}/checkout', [ServiceController::class, 'postCheckout'])->name('services.checkout.process');
    Route::post('/services/{service}/reviews', [ReviewController::class, 'storeService'])->name('services.reviews.store');
    Route::get('/shopping-cart', [ItemController::class, 'getCart'])->name('getCart');
    Route::get('/checkout', [ItemController::class, 'showCheckout'])->name('checkout');
    Route::post('/checkout', [ItemController::class, 'postCheckout'])->name('checkout.process');
    Route::post('/user/logout', [UserController::class, 'logout'])->name('user.logout');
    Route::resource('customers', CustomerController::class)->only(['create', 'store', 'show']);
    
    // Admin-only routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/customers', [DashboardController::class, 'getCustomers'])->name('admin.customers');
        Route::get('/users', [DashboardController::class, 'getUsers'])->name('admin.users');
        Route::get('/discount-codes', [DashboardController::class, 'discountCodes'])->name('admin.discount-codes');
        Route::get('/orders', [DashboardController::class, 'getOrders'])->name('admin.orders');
        Route::get('/reviews', [DashboardController::class, 'getReviews'])->name('admin.reviews');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
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

Auth::routes(['verify' => true]);
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

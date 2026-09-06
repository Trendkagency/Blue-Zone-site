<?php

use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\PageController;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\ShopController;
use Illuminate\Support\Facades\Route;

// Customer Public Storefront
Route::get('/', [HomeController::class, 'index'])->name('customer.home');
Route::get('/shop', [ShopController::class, 'index'])->name('customer.shop');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('customer.product.show');
Route::get('/cart', [CartController::class, 'index'])->name('customer.cart');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('customer.checkout');

// Customer Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('customer.auth.login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('customer.auth.register');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('customer.auth.forgot-password');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('customer.auth.reset-password');

// Customer Account Area
Route::prefix('account')->name('customer.account.')->group(function () {
    Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AccountController::class, 'showOrder'])->name('orders.show');
    Route::get('/invoices', [AccountController::class, 'invoices'])->name('invoices');
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
});

Route::get('/products', [ShopController::class, 'index'])->name('customer.products');
Route::get('/blog', [PageController::class, 'blog'])->name('customer.pages.blog');
Route::get('/about', [PageController::class, 'about'])->name('customer.pages.about');
Route::get('/science', [PageController::class, 'science'])->name('customer.pages.science');
Route::get('/team', [PageController::class, 'team'])->name('customer.pages.team');
Route::get('/contact', [PageController::class, 'contact'])->name('customer.pages.contact');
Route::get('/faqs', [PageController::class, 'faqs'])->name('customer.pages.faqs');
Route::get('/privacy', [PageController::class, 'privacy'])->name('customer.pages.privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('customer.pages.terms');

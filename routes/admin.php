<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\OfflineSaleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin Auth Routes
    Route::middleware(['guest:web'])->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    // Authenticated Admin Routes
    Route::middleware(['auth:web'])->group(function () {
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Admin Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Profile & Account Management
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::put('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');

        // Products & Categories Management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->middleware('permission:products.view')->name('index');
            Route::get('/create', [ProductController::class, 'create'])->middleware('permission:products.create')->name('create');
            Route::post('/', [ProductController::class, 'store'])->middleware('permission:products.create')->name('store');
            Route::get('/{id}', [ProductController::class, 'show'])->middleware('permission:products.view')->name('show');
            Route::get('/{id}/edit', [ProductController::class, 'edit'])->middleware('permission:products.edit')->name('edit');
            Route::put('/{id}', [ProductController::class, 'update'])->middleware('permission:products.edit')->name('update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->middleware('permission:products.delete')->name('destroy');
            Route::post('/{id}/restore', [ProductController::class, 'restore'])->middleware('permission:products.delete')->name('restore');
            Route::delete('/{id}/force-delete', [ProductController::class, 'forceDelete'])->middleware('permission:products.delete')->name('force-delete');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->middleware('permission:products.view')->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->middleware('permission:products.create')->name('create');
            Route::post('/', [CategoryController::class, 'store'])->middleware('permission:products.create')->name('store');
            Route::get('/{id}/edit', [CategoryController::class, 'edit'])->middleware('permission:products.edit')->name('edit');
            Route::put('/{id}', [CategoryController::class, 'update'])->middleware('permission:products.edit')->name('update');
            Route::delete('/{id}', [CategoryController::class, 'destroy'])->middleware('permission:products.delete')->name('destroy');
            Route::post('/{id}/restore', [CategoryController::class, 'restore'])->middleware('permission:products.delete')->name('restore');
            Route::delete('/{id}/force-delete', [CategoryController::class, 'forceDelete'])->middleware('permission:products.delete')->name('force-delete');
        });

        // Inventory & Stock Management
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->middleware('permission:inventory.view')->name('index');
            Route::post('/adjustments', [InventoryController::class, 'storeAdjustment'])->middleware('permission:inventory.create')->name('adjustments.store');
            Route::get('/transfers', [InventoryController::class, 'transfers'])->middleware('permission:inventory.view')->name('transfers');
            Route::post('/transfers', [InventoryController::class, 'storeTransfer'])->middleware('permission:inventory.create')->name('transfers.store');
            Route::get('/history', [InventoryController::class, 'history'])->middleware('permission:inventory.view')->name('history');
            Route::get('/{id}', [InventoryController::class, 'show'])->middleware('permission:inventory.view')->name('show');
        });

        // Order Management & Invoices
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->middleware('permission:orders.view')->name('index');
            Route::get('/{id}', [OrderController::class, 'show'])->middleware('permission:orders.view')->name('show');
            Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->middleware('permission:orders.edit')->name('update-status');
            Route::delete('/{id}', [OrderController::class, 'destroy'])->middleware('permission:orders.delete')->name('destroy');
            Route::post('/{id}/restore', [OrderController::class, 'restore'])->middleware('permission:orders.delete')->name('restore');
            Route::delete('/{id}/force-delete', [OrderController::class, 'forceDelete'])->middleware('permission:orders.delete')->name('force-delete');
        });

        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->middleware('permission:invoices.view')->name('index');
            Route::get('/{id}', [InvoiceController::class, 'show'])->middleware('permission:invoices.view')->name('show');
            Route::get('/{id}/print', [InvoiceController::class, 'print'])->middleware('permission:invoices.view')->name('print');
        });

        // Offline POS Sales Management
        Route::prefix('offline-sales')->name('offline-sales.')->group(function () {
            Route::get('/', [OfflineSaleController::class, 'index'])->middleware('permission:offline_sales.view')->name('index');
            Route::get('/create', [OfflineSaleController::class, 'create'])->middleware('permission:offline_sales.create')->name('create');
            Route::post('/', [OfflineSaleController::class, 'store'])->middleware('permission:offline_sales.create')->name('store');
            Route::get('/{id}', [OfflineSaleController::class, 'show'])->middleware('permission:offline_sales.view')->name('show');
        });

        // Customers CRM
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->middleware('permission:customers.view')->name('index');
            Route::get('/create', [CustomerController::class, 'create'])->middleware('permission:customers.create')->name('create');
            Route::post('/', [CustomerController::class, 'store'])->middleware('permission:customers.create')->name('store');
            Route::get('/{id}', [CustomerController::class, 'show'])->middleware('permission:customers.view')->name('show');
            Route::get('/{id}/edit', [CustomerController::class, 'edit'])->middleware('permission:customers.edit')->name('edit');
            Route::put('/{id}', [CustomerController::class, 'update'])->middleware('permission:customers.edit')->name('update');
            Route::delete('/{id}', [CustomerController::class, 'destroy'])->middleware('permission:customers.delete')->name('destroy');
            Route::post('/{id}/restore', [CustomerController::class, 'restore'])->middleware('permission:customers.delete')->name('restore');
            Route::delete('/{id}/force-delete', [CustomerController::class, 'forceDelete'])->middleware('permission:customers.delete')->name('force-delete');
        });

        // Reports & Print Dossier
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->middleware('permission:reports.view')->name('index');
            Route::get('/export', [ReportController::class, 'export'])->middleware('permission:reports.view')->name('export');
            Route::get('/print', [ReportController::class, 'print'])->middleware('permission:reports.view')->name('print');
        });

        // Content Management (CMS)
        Route::prefix('content')->name('content.')->group(function () {
            Route::get('/', [ContentController::class, 'index'])->middleware('permission:content.view')->name('index');
            Route::get('/banners', [ContentController::class, 'banners'])->middleware('permission:content.view')->name('banners');
            Route::post('/banners', [ContentController::class, 'updateBanners'])->middleware('permission:content.edit')->name('banners.update');
            Route::get('/story', [ContentController::class, 'story'])->middleware('permission:content.view')->name('story');
            Route::post('/story', [ContentController::class, 'updateStory'])->middleware('permission:content.edit')->name('story.update');
            Route::get('/wellness', [ContentController::class, 'wellness'])->middleware('permission:content.view')->name('wellness');
            Route::post('/wellness', [ContentController::class, 'updateWellness'])->middleware('permission:content.edit')->name('wellness.update');
            Route::get('/faqs', [ContentController::class, 'faqs'])->middleware('permission:content.view')->name('faqs');
            Route::post('/faqs', [ContentController::class, 'storeFaq'])->middleware('permission:content.create')->name('faqs.store');
            Route::put('/faqs/{id}', [ContentController::class, 'updateFaq'])->middleware('permission:content.edit')->name('faqs.update');
            Route::delete('/faqs/{id}', [ContentController::class, 'destroyFaq'])->middleware('permission:content.delete')->name('faqs.destroy');
        });

        // Users & Roles Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view')->name('index');
            Route::get('/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('create');
            Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create')->name('store');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->middleware('permission:users.edit')->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('destroy');
            Route::post('/{id}/restore', [UserController::class, 'restore'])->middleware('permission:users.delete')->name('restore');
            Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->middleware('permission:users.delete')->name('force-delete');
        });

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('index');
            Route::get('/create', [RoleController::class, 'create'])->middleware('permission:roles.create')->name('create');
            Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('store');
            Route::get('/{id}/edit', [RoleController::class, 'edit'])->middleware('permission:roles.edit')->name('edit');
            Route::put('/{id}', [RoleController::class, 'update'])->middleware('permission:roles.edit')->name('update');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('destroy');
            Route::post('/{id}/restore', [RoleController::class, 'restore'])->middleware('permission:roles.delete')->name('restore');
            Route::delete('/{id}/force-delete', [RoleController::class, 'forceDelete'])->middleware('permission:roles.delete')->name('force-delete');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->middleware('permission:settings.view')->name('index');
            Route::post('/', [SettingController::class, 'update'])->middleware('permission:settings.edit')->name('update');
        });
    });
});

<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\OfflineSaleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products Management
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');

    // Category Management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');

    // Inventory & Stock Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/transfers', [InventoryController::class, 'transfers'])->name('inventory.transfers');
    Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');
    Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');

    // Order Management (Online & Filterable)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

    // Offline Sales Management (POS Style)
    Route::get('/offline-sales', [OfflineSaleController::class, 'index'])->name('offline-sales.index');
    Route::get('/offline-sales/create', [OfflineSaleController::class, 'create'])->name('offline-sales.create');
    Route::get('/offline-sales/{id}', [OfflineSaleController::class, 'show'])->name('offline-sales.show');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Content Management (CMS)
    Route::get('/content', [ContentController::class, 'index'])->name('content.index');
    Route::get('/content/banners', [ContentController::class, 'banners'])->name('content.banners');
    Route::get('/content/story', [ContentController::class, 'story'])->name('content.story');
    Route::get('/content/wellness', [ContentController::class, 'wellness'])->name('content.wellness');
    Route::get('/content/faqs', [ContentController::class, 'faqs'])->name('content.faqs');

    // Users & Access Control
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');

    // Roles & Permissions Matrix
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
});

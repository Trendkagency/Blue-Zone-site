<?php

<<<<<<< HEAD
=======
use App\Http\Controllers\Admin\AuthController;
>>>>>>> origin/main
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\OfflineSaleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
<<<<<<< HEAD
=======
use App\Http\Controllers\Admin\ProfileController;
>>>>>>> origin/main
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
<<<<<<< HEAD
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
=======
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

        // Products Management (Full CRUD, Soft Delete, Restore, Force Delete)
        Route::middleware('permission:manage_products')->group(function () {
            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
            Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
            Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
            Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');

            // Category Management (Full CRUD, Soft Delete, Restore, Force Delete)
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
            Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
            Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
            Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
        });

        // Inventory & Stock Management
        Route::middleware('permission:manage_inventory')->group(function () {
            Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
            Route::post('/inventory/adjustments', [InventoryController::class, 'storeAdjustment'])->name('inventory.adjustments.store');
            Route::get('/inventory/transfers', [InventoryController::class, 'transfers'])->name('inventory.transfers');
            Route::post('/inventory/transfers', [InventoryController::class, 'storeTransfer'])->name('inventory.transfers.store');
            Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');
            Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');
        });

        // Order Management (Online & Filterable, Soft Delete, Restore, Force Delete)
        Route::middleware('permission:manage_orders')->group(function () {
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
            Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
            Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
            Route::post('/orders/{id}/restore', [OrderController::class, 'restore'])->name('orders.restore');
            Route::delete('/orders/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('orders.force-delete');

            Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
            Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
            Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        });

        // Offline Sales Management (POS Style)
        Route::middleware('permission:manage_offline_sales')->group(function () {
            Route::get('/offline-sales', [OfflineSaleController::class, 'index'])->name('offline-sales.index');
            Route::get('/offline-sales/create', [OfflineSaleController::class, 'create'])->name('offline-sales.create');
            Route::post('/offline-sales', [OfflineSaleController::class, 'store'])->name('offline-sales.store');
            Route::get('/offline-sales/{id}', [OfflineSaleController::class, 'show'])->name('offline-sales.show');
        });

        // Customers CRM (Full CRUD, Soft Delete, Restore, Force Delete)
        Route::middleware('permission:manage_customers')->group(function () {
            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
            Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
            Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
            Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
            Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
            Route::post('/customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
            Route::delete('/customers/{id}/force-delete', [CustomerController::class, 'forceDelete'])->name('customers.force-delete');
        });

        // Reports
        Route::middleware('permission:view_reports')->group(function () {
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        });

        // Content Management (CMS) & FAQs CRUD
        Route::middleware('permission:manage_cms')->group(function () {
            Route::get('/content', [ContentController::class, 'index'])->name('content.index');
            Route::get('/content/banners', [ContentController::class, 'banners'])->name('content.banners');
            Route::post('/content/banners', [ContentController::class, 'updateBanners'])->name('content.banners.update');
            Route::get('/content/story', [ContentController::class, 'story'])->name('content.story');
            Route::post('/content/story', [ContentController::class, 'updateStory'])->name('content.story.update');
            Route::get('/content/wellness', [ContentController::class, 'wellness'])->name('content.wellness');
            Route::post('/content/wellness', [ContentController::class, 'updateWellness'])->name('content.wellness.update');
            Route::get('/content/faqs', [ContentController::class, 'faqs'])->name('content.faqs');
            Route::post('/content/faqs', [ContentController::class, 'storeFaq'])->name('content.faqs.store');
            Route::put('/content/faqs/{id}', [ContentController::class, 'updateFaq'])->name('content.faqs.update');
            Route::delete('/content/faqs/{id}', [ContentController::class, 'destroyFaq'])->name('content.faqs.destroy');
        });

        // Users & Access Control (Full CRUD, Soft Delete, Restore, Force Delete)
        Route::middleware('permission:manage_users')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
            Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');

            // Roles & Permissions Matrix (Full CRUD, Soft Delete, Restore, Force Delete)
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
            Route::post('/roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
            Route::delete('/roles/{id}/force-delete', [RoleController::class, 'forceDelete'])->name('roles.force-delete');
        });

        // Settings
        Route::middleware('permission:manage_settings')->group(function () {
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        });
    });
>>>>>>> origin/main
});

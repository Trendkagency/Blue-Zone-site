<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CrmActivityController;
use App\Http\Controllers\Admin\CrmCampaignController;
use App\Http\Controllers\Admin\CrmCompanyController;
use App\Http\Controllers\Admin\CrmController;
use App\Http\Controllers\Admin\CrmLeadController;
use App\Http\Controllers\Admin\CrmOpportunityController;
use App\Http\Controllers\Admin\CrmPipelineController;
use App\Http\Controllers\Admin\CrmReportController;
use App\Http\Controllers\Admin\CrmSegmentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OfflineSaleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin Auth Routes
    Route::middleware(['guest:web'])->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.submit');
    });

    // Authenticated Admin Routes
    Route::middleware(['auth:web'])->group(function () {
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Admin Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->middleware('throttle:polling')->name('index');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/fcm-token', [NotificationController::class, 'updateFcmToken'])->middleware('throttle:polling')->name('fcm-token');
            Route::post('/test-push', [NotificationController::class, 'testPush'])->name('test-push');
        });

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

        // Inventory & Stock Management (including Drag & Drop Allocator & Control Hub)
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->middleware('permission:inventory.view')->name('index');
            Route::get('/control', [InventoryController::class, 'control'])->middleware('permission:inventory.view')->name('control');
            Route::post('/quick-adjust', [InventoryController::class, 'ajaxQuickAdjust'])->middleware('permission:inventory.create')->name('quick-adjust');
            Route::post('/batch-adjust', [InventoryController::class, 'ajaxBatchAdjust'])->middleware('permission:inventory.create')->name('batch-adjust');
            Route::get('/allocator', [InventoryController::class, 'allocator'])->middleware('permission:inventory.view')->name('allocator');
            Route::post('/allocator/transfer', [InventoryController::class, 'ajaxTransfer'])->middleware('permission:inventory.create')->name('allocator.transfer');
            Route::post('/allocator/batch-split', [InventoryController::class, 'ajaxBatchSplit'])->middleware('permission:inventory.create')->name('allocator.batch_split');
            Route::post('/adjustments', [InventoryController::class, 'storeAdjustment'])->middleware('permission:inventory.create')->name('adjustments.store');
            Route::prefix('warehouses')->name('warehouses.')->group(function () {
                Route::get('/', [WarehouseController::class, 'index'])->middleware('permission:inventory.view')->name('index');
                Route::get('/create', [WarehouseController::class, 'create'])->middleware('permission:inventory.create')->name('create');
                Route::post('/', [WarehouseController::class, 'store'])->middleware('permission:inventory.create')->name('store');
                Route::get('/{id}', [WarehouseController::class, 'show'])->middleware('permission:inventory.view')->name('show');
                Route::get('/{id}/edit', [WarehouseController::class, 'edit'])->middleware('permission:inventory.edit')->name('edit');
                Route::put('/{id}', [WarehouseController::class, 'update'])->middleware('permission:inventory.edit')->name('update');
                Route::post('/{id}/toggle-status', [WarehouseController::class, 'toggleStatus'])->middleware('permission:inventory.edit')->name('toggle-status');
                Route::delete('/{id}', [WarehouseController::class, 'destroy'])->middleware('permission:inventory.delete')->name('destroy');
            });
            Route::get('/transfers', [InventoryController::class, 'transfers'])->middleware('permission:inventory.view')->name('transfers');
            Route::post('/transfers', [InventoryController::class, 'storeTransfer'])->middleware('permission:inventory.create')->name('transfers.store');
            Route::get('/history', [InventoryController::class, 'history'])->middleware('permission:inventory.view')->name('history');
            Route::get('/{id}', [InventoryController::class, 'show'])->middleware('permission:inventory.view')->name('show');
        });

        // System Locations & Facilities Control Center
        Route::prefix('locations')->name('locations.')->group(function () {
            Route::get('/', [LocationController::class, 'index'])->middleware('permission:inventory.view')->name('index');
            Route::get('/create', [LocationController::class, 'create'])->middleware('permission:inventory.create')->name('create');
            Route::post('/', [LocationController::class, 'store'])->middleware('permission:inventory.create')->name('store');
            Route::get('/{id}', [LocationController::class, 'show'])->middleware('permission:inventory.view')->name('show');
            Route::get('/{id}/edit', [LocationController::class, 'edit'])->middleware('permission:inventory.edit')->name('edit');
            Route::put('/{id}', [LocationController::class, 'update'])->middleware('permission:inventory.edit')->name('update');
            Route::post('/{id}/toggle-status', [LocationController::class, 'toggleStatus'])->middleware('permission:inventory.edit')->name('toggle-status');
            Route::delete('/{id}', [LocationController::class, 'destroy'])->middleware('permission:inventory.delete')->name('destroy');
        });

        // Warehouses Top-Level Alias
        Route::prefix('warehouses')->name('warehouses.')->group(function () {
            Route::get('/', [WarehouseController::class, 'index'])->middleware('permission:inventory.view')->name('index');
            Route::get('/create', [WarehouseController::class, 'create'])->middleware('permission:inventory.create')->name('create');
            Route::post('/', [WarehouseController::class, 'store'])->middleware('permission:inventory.create')->name('store');
            Route::get('/{id}', [WarehouseController::class, 'show'])->middleware('permission:inventory.view')->name('show');
            Route::get('/{id}/edit', [WarehouseController::class, 'edit'])->middleware('permission:inventory.edit')->name('edit');
            Route::put('/{id}', [WarehouseController::class, 'update'])->middleware('permission:inventory.edit')->name('update');
            Route::post('/{id}/toggle-status', [WarehouseController::class, 'toggleStatus'])->middleware('permission:inventory.edit')->name('toggle-status');
            Route::delete('/{id}', [WarehouseController::class, 'destroy'])->middleware('permission:inventory.delete')->name('destroy');
        });

        // Order Management & Invoices
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->middleware('permission:orders.view')->name('index');
            Route::get('/{id}', [OrderController::class, 'show'])->middleware('permission:orders.view')->name('show');
            Route::match(['post', 'patch'], '/{id}/status', [OrderController::class, 'updateStatus'])->middleware('permission:orders.edit')->name('update-status');
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
            Route::post('/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->middleware('permission:customers.edit')->name('toggle-status');
            Route::get('/{id}/crm-360', [CustomerController::class, 'crm360'])->middleware('permission:crm.contacts.view')->name('crm-360');
        });

        // Full CRM System
        Route::prefix('crm')->name('crm.')->group(function () {
            // Dashboard
            Route::get('/', [CrmController::class, 'index'])->middleware('permission:crm.dashboard')->name('dashboard');

            // Leads
            Route::prefix('leads')->name('leads.')->group(function () {
                Route::get('/', [CrmLeadController::class, 'index'])->middleware('permission:crm.leads.view')->name('index');
                Route::get('/create', [CrmLeadController::class, 'create'])->middleware('permission:crm.leads.create')->name('create');
                Route::post('/', [CrmLeadController::class, 'store'])->middleware('permission:crm.leads.create')->name('store');
                Route::get('/export', [CrmLeadController::class, 'export'])->middleware('permission:crm.reports.export')->name('export');
                Route::get('/check-duplicates', [CrmLeadController::class, 'checkDuplicates'])->middleware('permission:crm.leads.view')->name('check-duplicates');
                Route::post('/bulk-assign', [CrmLeadController::class, 'bulkAssign'])->middleware('permission:crm.leads.assign')->name('bulk-assign');
                Route::post('/bulk-status', [CrmLeadController::class, 'bulkStatus'])->middleware('permission:crm.leads.edit')->name('bulk-status');
                Route::get('/{id}', [CrmLeadController::class, 'show'])->middleware('permission:crm.leads.view')->name('show');
                Route::get('/{id}/edit', [CrmLeadController::class, 'edit'])->middleware('permission:crm.leads.edit')->name('edit');
                Route::put('/{id}', [CrmLeadController::class, 'update'])->middleware('permission:crm.leads.edit')->name('update');
                Route::delete('/{id}', [CrmLeadController::class, 'destroy'])->middleware('permission:crm.leads.delete')->name('destroy');
                Route::post('/{id}/convert', [CrmLeadController::class, 'convert'])->middleware('permission:crm.leads.convert')->name('convert');
            });

            // Opportunities
            Route::prefix('opportunities')->name('opportunities.')->group(function () {
                Route::get('/', [CrmOpportunityController::class, 'index'])->middleware('permission:crm.opportunities.view')->name('index');
                Route::get('/create', [CrmOpportunityController::class, 'create'])->middleware('permission:crm.opportunities.create')->name('create');
                Route::post('/', [CrmOpportunityController::class, 'store'])->middleware('permission:crm.opportunities.create')->name('store');
                Route::get('/{id}', [CrmOpportunityController::class, 'show'])->middleware('permission:crm.opportunities.view')->name('show');
                Route::post('/{id}/stage', [CrmOpportunityController::class, 'updateStage'])->middleware('permission:crm.opportunities.edit')->name('update-stage');
                Route::delete('/{id}', [CrmOpportunityController::class, 'destroy'])->middleware('permission:crm.opportunities.delete')->name('destroy');
            });

            // Activities / Tasks
            Route::prefix('activities')->name('activities.')->group(function () {
                Route::get('/', [CrmActivityController::class, 'index'])->middleware('permission:crm.activities.view')->name('index');
                Route::get('/create', [CrmActivityController::class, 'create'])->middleware('permission:crm.activities.create')->name('create');
                Route::post('/', [CrmActivityController::class, 'store'])->middleware('permission:crm.activities.create')->name('store');
                Route::post('/{id}/complete', [CrmActivityController::class, 'complete'])->middleware('permission:crm.activities.edit')->name('complete');
                Route::post('/{id}/cancel', [CrmActivityController::class, 'cancel'])->middleware('permission:crm.activities.edit')->name('cancel');
                Route::delete('/{id}', [CrmActivityController::class, 'destroy'])->middleware('permission:crm.activities.delete')->name('destroy');
            });

            // B2B Companies
            Route::prefix('companies')->name('companies.')->group(function () {
                Route::get('/', [CrmCompanyController::class, 'index'])->middleware('permission:crm.companies.view')->name('index');
                Route::get('/create', [CrmCompanyController::class, 'create'])->middleware('permission:crm.companies.create')->name('create');
                Route::post('/', [CrmCompanyController::class, 'store'])->middleware('permission:crm.companies.create')->name('store');
                Route::get('/{id}', [CrmCompanyController::class, 'show'])->middleware('permission:crm.companies.view')->name('show');
                Route::get('/{id}/edit', [CrmCompanyController::class, 'edit'])->middleware('permission:crm.companies.edit')->name('edit');
                Route::put('/{id}', [CrmCompanyController::class, 'update'])->middleware('permission:crm.companies.edit')->name('update');
                Route::delete('/{id}', [CrmCompanyController::class, 'destroy'])->middleware('permission:crm.companies.delete')->name('destroy');
            });

            // Marketing Campaigns
            Route::prefix('campaigns')->name('campaigns.')->group(function () {
                Route::get('/', [CrmCampaignController::class, 'index'])->middleware('permission:crm.campaigns.manage')->name('index');
                Route::get('/create', [CrmCampaignController::class, 'create'])->middleware('permission:crm.campaigns.manage')->name('create');
                Route::post('/', [CrmCampaignController::class, 'store'])->middleware('permission:crm.campaigns.manage')->name('store');
                Route::get('/{id}', [CrmCampaignController::class, 'show'])->middleware('permission:crm.campaigns.manage')->name('show');
                Route::get('/{id}/edit', [CrmCampaignController::class, 'edit'])->middleware('permission:crm.campaigns.manage')->name('edit');
                Route::put('/{id}', [CrmCampaignController::class, 'update'])->middleware('permission:crm.campaigns.manage')->name('update');
                Route::delete('/{id}', [CrmCampaignController::class, 'destroy'])->middleware('permission:crm.campaigns.manage')->name('destroy');
            });

            // Pipelines & Stages
            Route::prefix('pipelines')->name('pipelines.')->group(function () {
                Route::get('/', [CrmPipelineController::class, 'index'])->middleware('permission:crm.pipelines.manage')->name('index');
                Route::post('/', [CrmPipelineController::class, 'store'])->middleware('permission:crm.pipelines.manage')->name('store');
                Route::post('/{id}/stages', [CrmPipelineController::class, 'storeStage'])->middleware('permission:crm.pipelines.manage')->name('stages.store');
            });

            // Dynamic Segments
            Route::prefix('segments')->name('segments.')->group(function () {
                Route::get('/', [CrmSegmentController::class, 'index'])->middleware('permission:crm.segments.manage')->name('index');
                Route::get('/{id}', [CrmSegmentController::class, 'show'])->middleware('permission:crm.segments.manage')->name('show');
                Route::post('/refresh', [CrmSegmentController::class, 'refresh'])->middleware('permission:crm.segments.manage')->name('refresh');
            });

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [CrmReportController::class, 'index'])->middleware('permission:crm.reports.view')->name('index');
            });
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
            Route::get('/{id}', [UserController::class, 'show'])->middleware('permission:users.view')->name('show');
            Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:users.edit')->name('toggle-status');
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

        // Settings & Geographic Hierarchy (Countries & Cities)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->middleware('permission:settings.view')->name('index');
            Route::post('/', [SettingController::class, 'update'])->middleware('permission:settings.edit')->name('update');
            Route::get('/geo', [CountryController::class, 'index'])->middleware('permission:settings.view')->name('geo.index');
        });

        // Countries Management
        Route::prefix('countries')->name('countries.')->group(function () {
            Route::get('/', [CountryController::class, 'index'])->middleware('permission:settings.view')->name('index');
            Route::post('/', [CountryController::class, 'store'])->middleware('permission:settings.edit')->name('store');
            Route::put('/{id}', [CountryController::class, 'update'])->middleware('permission:settings.edit')->name('update');
            Route::post('/{id}/toggle-status', [CountryController::class, 'toggleStatus'])->middleware('permission:settings.edit')->name('toggle-status');
            Route::delete('/{id}', [CountryController::class, 'destroy'])->middleware('permission:settings.edit')->name('destroy');
        });

        // Cities Management
        Route::prefix('cities')->name('cities.')->group(function () {
            Route::post('/', [CityController::class, 'store'])->middleware('permission:settings.edit')->name('store');
            Route::put('/{id}', [CityController::class, 'update'])->middleware('permission:settings.edit')->name('update');
            Route::post('/{id}/toggle-status', [CityController::class, 'toggleStatus'])->middleware('permission:settings.edit')->name('toggle-status');
            Route::delete('/{id}', [CityController::class, 'destroy'])->middleware('permission:settings.edit')->name('destroy');
        });

        // Dynamic Cascading Geo API Endpoint
        Route::get('/api/countries/{id}/cities', [CityController::class, 'getCitiesByCountry'])->name('api.countries.cities');
    });
});

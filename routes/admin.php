<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BreakController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CrmActivityController;
use App\Http\Controllers\Admin\CrmCampaignController;
use App\Http\Controllers\Admin\CrmCompanyController;
use App\Http\Controllers\Admin\CrmController;
use App\Http\Controllers\Admin\CrmLeadController;
use App\Http\Controllers\Admin\CrmLeadImportController;
use App\Http\Controllers\Admin\CrmOpportunityController;
use App\Http\Controllers\Admin\CrmPipelineController;
use App\Http\Controllers\Admin\CrmReportController;
use App\Http\Controllers\Admin\CrmSalesExcelController;
use App\Http\Controllers\Admin\CrmSegmentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\Finance\FinanceDashboardController;
use App\Http\Controllers\Admin\Finance\SalesInvoiceController;
use App\Http\Controllers\Admin\Finance\PurchaseInvoiceController;
use App\Http\Controllers\Admin\Finance\CustomerPaymentController;
use App\Http\Controllers\Admin\Finance\SupplierPaymentController;
use App\Http\Controllers\Admin\Finance\ExpenseController;
use App\Http\Controllers\Admin\Finance\FinancialAccountController;
use App\Http\Controllers\Admin\Finance\CollectionEntryController;
use App\Http\Controllers\Admin\Finance\PaymentEntryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\Mr\AssignmentController as MrAssignmentController;
use App\Http\Controllers\Admin\Mr\ClassificationController as MrClassificationController;
use App\Http\Controllers\Admin\Mr\ContactController as MrContactController;
use App\Http\Controllers\Admin\Mr\CycleController as MrCycleController;
use App\Http\Controllers\Admin\Mr\GpsConfigController as MrGpsConfigController;
use App\Http\Controllers\Admin\Mr\MrDashboardController;
use App\Http\Controllers\Admin\Mr\MrMapController;
use App\Http\Controllers\Admin\Mr\MrReportController;
use App\Http\Controllers\Admin\Mr\SpecialtyController as MrSpecialtyController;
use App\Http\Controllers\Admin\Mr\VisitController as MrVisitController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StockAdjustmentTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Hr\AssetController as HrAssetController;
use App\Http\Controllers\Admin\Hr\AttendanceController as HrAttendanceController;
use App\Http\Controllers\Admin\Hr\DepartmentController as HrDepartmentController;
use App\Http\Controllers\Admin\Hr\EmployeeController as HrEmployeeController;
use App\Http\Controllers\Admin\Hr\EmployeeDocumentController as HrEmployeeDocumentController;
use App\Http\Controllers\Admin\Hr\EmployeeRequestController as HrEmployeeRequestController;
use App\Http\Controllers\Admin\Hr\HrDashboardController;
use App\Http\Controllers\Admin\Hr\HrReportController;
use App\Http\Controllers\Admin\Hr\HrSettingController;
use App\Http\Controllers\Admin\Hr\LeaveController as HrLeaveController;
use App\Http\Controllers\Admin\Hr\OffboardingController as HrOffboardingController;
use App\Http\Controllers\Admin\Hr\PayrollController as HrPayrollController;
use App\Http\Controllers\Admin\Hr\PerformanceController as HrPerformanceController;
use App\Http\Controllers\Admin\Hr\PositionController as HrPositionController;
use App\Http\Controllers\Admin\Hr\RecruitmentController as HrRecruitmentController;
use App\Http\Controllers\Admin\Hr\SelfServiceController;
use App\Http\Controllers\Admin\Hr\TrainingController as HrTrainingController;
use App\Http\Controllers\Admin\Hr\WorkScheduleController as HrWorkScheduleController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\RawMaterialController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\GoodsReceiptController;
use App\Http\Controllers\Admin\RawMaterialBatchController;
use App\Http\Controllers\Admin\QcInspectionController;
use App\Http\Controllers\Admin\WorkOrderController;
use App\Http\Controllers\Admin\SupplyChainReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Assets proxy fallback to prevent relative 404s when requests are made under /admin/assets/*
    Route::get('/assets/{path}', function ($path) {
        $filePath = public_path('assets/' . $path);
        if (file_exists($filePath)) {
            return response()->file($filePath);
        }
        return redirect('/assets/' . $path, 301);
    })->where('path', '.*')->name('assets.fallback');

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
            Route::post('/landing-section-settings', [ProductController::class, 'updateLandingSectionSettings'])->middleware('permission:products.edit')->name('landing-section-settings');
            Route::post('/details-section-settings', [ProductController::class, 'updateDetailsSectionSettings'])->middleware('permission:products.edit')->name('details-section-settings');
            Route::post('/button-settings', [ProductController::class, 'updateButtonSettings'])->middleware('permission:products.edit')->name('button-settings');
            Route::post('/{id}/toggle-landing', [ProductController::class, 'toggleLandingVisibility'])->middleware('permission:products.edit')->name('toggle-landing');
            Route::get('/create', [ProductController::class, 'create'])->middleware('permission:products.create')->name('create');
            Route::post('/', [ProductController::class, 'store'])->middleware('permission:products.create')->name('store');
            Route::get('/{id}', [ProductController::class, 'show'])->middleware('permission:products.view')->name('show');
            Route::get('/{id}/edit', [ProductController::class, 'edit'])->middleware('permission:products.edit')->name('edit');
            Route::put('/{id}', [ProductController::class, 'update'])->middleware('permission:products.edit')->name('update');
            Route::match(['post', 'put'], '/auto-create-draft', [ProductController::class, 'autoCreateDraft'])->middleware('permission:products.create')->name('auto-create-draft');
            Route::match(['post', 'put'], '/{id}/auto-save-step', [ProductController::class, 'autoSaveStep'])->middleware('permission:products.edit')->name('auto-save-step');
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

        // Hero Slider & Product Carousel Command Center
        Route::prefix('hero-slider')->name('hero-slides.')->group(function () {
            Route::get('/', [HeroSlideController::class, 'index'])->middleware('permission:products.view')->name('index');
            Route::post('/', [HeroSlideController::class, 'store'])->middleware('permission:products.edit')->name('store');
            Route::get('/{id}', [HeroSlideController::class, 'show'])->middleware('permission:products.view')->name('show');
            Route::put('/{id}', [HeroSlideController::class, 'update'])->middleware('permission:products.edit')->name('update');
            Route::delete('/{id}', [HeroSlideController::class, 'destroy'])->middleware('permission:products.delete')->name('destroy');
            Route::post('/{id}/toggle-status', [HeroSlideController::class, 'toggleStatus'])->middleware('permission:products.edit')->name('toggle-status');
            Route::post('/reorder', [HeroSlideController::class, 'reorder'])->middleware('permission:products.edit')->name('reorder');
            Route::post('/global-settings', [HeroSlideController::class, 'updateGlobalSettings'])->middleware('permission:products.edit')->name('global-settings');
            Route::post('/reset-defaults', [HeroSlideController::class, 'resetDefaults'])->middleware('permission:products.edit')->name('reset-defaults');
        });

        // Inventory & Stock Management (including Drag & Drop Allocator & Control Hub)
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'dashboard'])->middleware('permission:inventory.view')->name('index');
            Route::get('/dashboard', [InventoryController::class, 'dashboard'])->middleware('permission:inventory.view')->name('dashboard');
            Route::get('/overview', [InventoryController::class, 'index'])->middleware('permission:inventory.view')->name('overview');
            Route::get('/control', [InventoryController::class, 'control'])->middleware('permission:inventory.view')->name('control');
            Route::get('/movements', [InventoryController::class, 'movements'])->middleware('permission:inventory.view')->name('movements');
            Route::get('/adjustments', [InventoryController::class, 'adjustments'])->middleware('permission:inventory.view')->name('adjustments');
            Route::get('/alerts', [InventoryController::class, 'alerts'])->middleware('permission:inventory.view')->name('alerts');
            Route::post('/quick-adjust', [InventoryController::class, 'ajaxQuickAdjust'])->middleware('permission:inventory.create')->name('quick-adjust');
            Route::post('/batch-adjust', [InventoryController::class, 'ajaxBatchAdjust'])->middleware('permission:inventory.create')->name('batch-adjust');
            Route::get('/allocator', [InventoryController::class, 'allocator'])->middleware('permission:inventory.view')->name('allocator');
            Route::post('/allocator/transfer', [InventoryController::class, 'ajaxTransfer'])->middleware('permission:inventory.create')->name('allocator.transfer');
            Route::post('/allocator/batch-split', [InventoryController::class, 'ajaxBatchSplit'])->middleware('permission:inventory.create')->name('allocator.batch_split');
            Route::post('/adjustments', [InventoryController::class, 'storeAdjustment'])->middleware('permission:inventory.create')->name('adjustments.store');
            Route::prefix('adjustments/types')->name('adjustments.types.')->group(function () {
                Route::get('/', [StockAdjustmentTypeController::class, 'index'])->middleware('permission:inventory.view')->name('index');
                Route::post('/', [StockAdjustmentTypeController::class, 'store'])->middleware('permission:inventory.create')->name('store');
                Route::post('/quick', [StockAdjustmentTypeController::class, 'quickStore'])->middleware('permission:inventory.create')->name('quick-store');
                Route::put('/{id}', [StockAdjustmentTypeController::class, 'update'])->middleware('permission:inventory.edit')->name('update');
                Route::post('/{id}/toggle-status', [StockAdjustmentTypeController::class, 'toggleStatus'])->middleware('permission:inventory.edit')->name('toggle-status');
                Route::delete('/{id}', [StockAdjustmentTypeController::class, 'destroy'])->middleware('permission:inventory.delete')->name('destroy');
            });
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
            Route::get('/{id}/label', [OrderController::class, 'averyLabel'])->middleware('permission:orders.view')->name('label');
            Route::match(['post', 'patch'], '/{id}/status', [OrderController::class, 'updateStatus'])->middleware('permission:orders.edit')->name('update-status');
            Route::delete('/{id}', [OrderController::class, 'destroy'])->middleware('permission:orders.delete')->name('destroy');
            Route::post('/{id}/restore', [OrderController::class, 'restore'])->middleware('permission:orders.delete')->name('restore');
            Route::delete('/{id}/force-delete', [OrderController::class, 'forceDelete'])->middleware('permission:orders.delete')->name('force-delete');
        });

        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->middleware('permission:invoices.view')->name('index');
            Route::get('/{id}', [InvoiceController::class, 'show'])->middleware('permission:invoices.view')->name('show');
            Route::match(['post', 'patch'], '/{id}', [InvoiceController::class, 'update'])->middleware('permission:orders.edit')->name('update');
            Route::get('/{id}/print', [InvoiceController::class, 'print'])->middleware('permission:invoices.view')->name('print');
            Route::get('/{id}/label', [InvoiceController::class, 'averyLabel'])->middleware('permission:invoices.view')->name('label');
        });

        // Commercial Sales Management (Products & Services, Quotations, Sales Orders)
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/products-services', [SalesController::class, 'productsServices'])->middleware('permission:products.view')->name('products-services');
            Route::get('/orders', [SalesController::class, 'orders'])->middleware('permission:orders.view')->name('orders.index');

            Route::prefix('quotations')->name('quotations.')->group(function () {
                Route::get('/', [QuotationController::class, 'index'])->middleware('permission:orders.view')->name('index');
                Route::get('/create', [QuotationController::class, 'create'])->middleware('permission:orders.create')->name('create');
                Route::post('/', [QuotationController::class, 'store'])->middleware('permission:orders.create')->name('store');
                Route::get('/{id}', [QuotationController::class, 'show'])->middleware('permission:orders.view')->name('show');
                Route::get('/{id}/print', [QuotationController::class, 'print'])->middleware('permission:orders.view')->name('print');
                Route::post('/{id}/convert', [QuotationController::class, 'convert'])->middleware('permission:orders.create')->name('convert');
                Route::match(['post', 'patch'], '/{id}/status', [QuotationController::class, 'updateStatus'])->middleware('permission:orders.edit')->name('update-status');
            });
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

        // Native Medical Representative (MR) Visit Management System
        Route::prefix('mr')->name('mr.')->group(function () {
            // Main CRM Executive Dashboard & Scheduling Command Center
            Route::get('/', [MrDashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard', [MrDashboardController::class, 'index'])->name('dashboard.alt');
            Route::post('/schedules/quick-create', [MrDashboardController::class, 'quickSchedule'])->name('schedules.quick-create');
            Route::post('/schedules/bulk-create', [MrDashboardController::class, 'bulkSchedule'])->name('schedules.bulk-create');
            Route::post('/schedules/{id}/status', [MrDashboardController::class, 'quickUpdateStatus'])->name('schedules.update-status');
            Route::post('/schedules/{id}/reschedule', [MrDashboardController::class, 'reschedule'])->name('schedules.reschedule');
            Route::delete('/schedules/{id}', [MrDashboardController::class, 'deleteSchedule'])->name('schedules.delete');
            Route::get('/reps/{id}/details-json', [MrDashboardController::class, 'repDetails'])->name('reps.details-json');
            Route::post('/visits/quick-record', [MrDashboardController::class, 'quickRecordVisit'])->name('visits.quick-record');
            Route::get('/calendar-feed', [MrDashboardController::class, 'calendarFeed'])->name('calendar-feed');

            // Live Ops Map (Admin & Line Managers Only)
            Route::get('/map', [MrMapController::class, 'index'])->name('live-map')->middleware('mr.manager');

            // Doctors & Clinics (Contacts)
            Route::prefix('contacts')->name('contacts.')->group(function () {
                Route::get('/', [MrContactController::class, 'index'])->name('index');
                Route::get('/create', [MrContactController::class, 'create'])->name('create');
                Route::post('/', [MrContactController::class, 'store'])->name('store');
                Route::post('/resolve-map-url', [MrContactController::class, 'resolveMapUrl'])->name('resolve_map_url');
                Route::get('/{id}', [MrContactController::class, 'show'])->name('show');
                Route::get('/{id}/quota', [MrContactController::class, 'quotaJson'])->name('quota');
                Route::get('/{id}/edit', [MrContactController::class, 'edit'])->name('edit');
                Route::put('/{id}', [MrContactController::class, 'update'])->name('update');
                Route::delete('/{id}', [MrContactController::class, 'destroy'])->name('destroy');
            });

            // Doctor Classifications (A+/A/B/C)
            Route::prefix('classifications')->name('classifications.')->middleware('mr.manager')->group(function () {
                Route::get('/', [MrClassificationController::class, 'index'])->name('index');
                Route::post('/', [MrClassificationController::class, 'store'])->name('store');
                Route::put('/{id}', [MrClassificationController::class, 'update'])->name('update');
                Route::delete('/{id}', [MrClassificationController::class, 'destroy'])->name('destroy');
            });

            // Medical Specialties
            Route::prefix('specialties')->name('specialties.')->middleware('mr.manager')->group(function () {
                Route::get('/', [MrSpecialtyController::class, 'index'])->name('index');
                Route::post('/', [MrSpecialtyController::class, 'store'])->name('store');
                Route::put('/{id}', [MrSpecialtyController::class, 'update'])->name('update');
                Route::delete('/{id}', [MrSpecialtyController::class, 'destroy'])->name('destroy');
            });

            // Doctor Assignments
            Route::prefix('assignments')->name('assignments.')->group(function () {
                Route::get('/', [MrAssignmentController::class, 'index'])->name('index');
                Route::post('/', [MrAssignmentController::class, 'store'])->name('store');
                Route::delete('/{id}', [MrAssignmentController::class, 'destroy'])->name('destroy');
            });

            // Visit Cycles
            Route::prefix('cycles')->name('cycles.')->middleware('mr.manager')->group(function () {
                Route::get('/', [MrCycleController::class, 'index'])->name('index');
                Route::post('/', [MrCycleController::class, 'store'])->name('store');
                Route::put('/{id}', [MrCycleController::class, 'update'])->name('update');
                Route::delete('/{id}', [MrCycleController::class, 'destroy'])->name('destroy');
            });

            // Executed Visits & GPS Log / Daily Agenda Management
            Route::prefix('visits')->name('visits.')->group(function () {
                Route::get('/', [MrVisitController::class, 'index'])->name('index');
                Route::post('/schedule', [MrVisitController::class, 'schedule'])->name('schedule');
                Route::post('/record-direct', [MrVisitController::class, 'recordDirectVisit'])->name('record-direct');
                Route::delete('/schedule/{id}', [MrVisitController::class, 'cancelSchedule'])->name('cancel-schedule');
                Route::get('/{id}', [MrVisitController::class, 'show'])->name('show');
            });

            // Reports (§8)
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/coverage', [MrReportController::class, 'coverage'])->name('coverage');
                Route::get('/performance', [MrReportController::class, 'performance'])->name('performance');
            });

            // GPS Geofence & Rules Config
            Route::prefix('gps-config')->name('gps-config.')->middleware('mr.manager')->group(function () {
                Route::get('/', [MrGpsConfigController::class, 'index'])->name('index');
                Route::post('/global', [MrGpsConfigController::class, 'updateGlobal'])->name('update-global');
                Route::post('/rep', [MrGpsConfigController::class, 'storeRepConfig'])->name('store-rep');
                Route::delete('/rep/{id}', [MrGpsConfigController::class, 'destroyRepConfig'])->name('destroy-rep');
            });

            // Territories & Areas Management
            Route::prefix('areas')->name('areas.')->middleware('mr.manager')->group(function () {
                Route::get('/', [AreaController::class, 'index'])->name('index');
                Route::post('/', [AreaController::class, 'store'])->name('store');
                Route::put('/{id}', [AreaController::class, 'update'])->name('update');
                Route::post('/{id}/toggle-status', [AreaController::class, 'toggleStatus'])->name('toggle-status');
                Route::delete('/{id}', [AreaController::class, 'destroy'])->name('destroy');
            });

            // Territories & Breaks Management
            Route::prefix('breaks')->name('breaks.')->middleware('mr.manager')->group(function () {
                Route::get('/', [BreakController::class, 'index'])->name('index');
                Route::post('/', [BreakController::class, 'store'])->name('store');
                Route::put('/{id}', [BreakController::class, 'update'])->name('update');
                Route::post('/{id}/toggle-status', [BreakController::class, 'toggleStatus'])->name('toggle-status');
                Route::delete('/{id}', [BreakController::class, 'destroy'])->name('destroy');
            });

            // MR Excel Hub (Doctors & Field Operations Batch Import, Multi-criteria Export & Live Spreadsheet Grid)
            Route::prefix('excel')->name('excel.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\Mr\MrExcelController::class, 'index'])->name('index');
                Route::post('/import', [\App\Http\Controllers\Admin\Mr\MrExcelController::class, 'import'])->name('import');
                Route::post('/preview', [\App\Http\Controllers\Admin\Mr\MrExcelController::class, 'preview'])->name('preview');
                Route::get('/template', [\App\Http\Controllers\Admin\Mr\MrExcelController::class, 'downloadTemplate'])->name('template');
                Route::get('/export', [\App\Http\Controllers\Admin\Mr\MrExcelController::class, 'export'])->name('export');
                Route::post('/manual-save', [\App\Http\Controllers\Admin\Mr\MrExcelController::class, 'saveManualGrid'])->name('manual.save');
                Route::post('/manual-export', [\App\Http\Controllers\Admin\Mr\MrExcelController::class, 'exportManualGrid'])->name('manual.export');
            });
        });

        // Redirects to Native MR Routes for Backward Compatibility
        Route::get('/areas', fn () => redirect()->route('admin.mr.areas.index'))->name('areas.index');
        Route::get('/breaks', fn () => redirect()->route('admin.mr.breaks.index'))->name('breaks.index');
        Route::get('/mr-live-ops-map', fn () => redirect()->route('admin.mr.live-map'))->name('mr-live-ops-map');
        Route::get('/contacts', fn () => redirect()->route('admin.mr.contacts.index'))->name('contacts.index');
        Route::get('/contacts/create', fn () => redirect()->route('admin.mr.contacts.create'))->name('contacts.create');
        Route::get('/contact-classifications', fn () => redirect()->route('admin.mr.classifications.index'))->name('contact-classifications.index');
        Route::get('/contact-specialties', fn () => redirect()->route('admin.mr.specialties.index'))->name('contact-specialties.index');
        Route::get('/contact-assignments', fn () => redirect()->route('admin.mr.assignments.index'))->name('contact-assignments.index');
        Route::get('/visit-cycles', fn () => redirect()->route('admin.mr.cycles.index'))->name('visit-cycles.index');
        Route::get('/visits', fn () => redirect()->route('admin.mr.visits.index'))->name('visits.index');
        Route::get('/unvisited-coverage-report', fn () => redirect()->route('admin.mr.reports.coverage'))->name('unvisited-coverage-report');
        Route::get('/rep-performance-report', fn () => redirect()->route('admin.mr.reports.performance'))->name('rep-performance-report');

        Route::prefix('mr-crm')->name('mr-crm.')->group(function () {
            Route::get('/', fn () => redirect()->route('admin.mr.dashboard'))->name('dashboard');
            Route::get('/live-map', fn () => redirect()->route('admin.mr.live-map'))->name('live-map');
            Route::get('/contacts', fn () => redirect()->route('admin.mr.contacts.index'))->name('contacts');
            Route::get('/assignments', fn () => redirect()->route('admin.mr.assignments.index'))->name('assignments');
            Route::get('/reports', fn () => redirect()->route('admin.mr.reports.coverage'))->name('reports');
        });

        // Full Commercial CRM System (Leads, Pipelines, Opportunities, Customer 360, Activities)
        Route::prefix('crm')->name('crm.')->middleware('permission:crm.view')->group(function () {
            // Dashboard
            Route::get('/', [CrmController::class, 'index'])->name('dashboard');

            // Sales Excel Hub (Batch Import, Multi-filter Export & Live Manual Spreadsheet Grid)
            Route::prefix('excel')->name('excel.')->group(function () {
                Route::get('/', [CrmSalesExcelController::class, 'index'])->name('index');
                Route::post('/import', [CrmSalesExcelController::class, 'import'])->name('import');
                Route::post('/preview', [CrmSalesExcelController::class, 'preview'])->name('preview');
                Route::get('/template', [CrmSalesExcelController::class, 'downloadTemplate'])->name('template');
                Route::get('/export', [CrmSalesExcelController::class, 'export'])->name('export');
                Route::post('/manual-save', [CrmSalesExcelController::class, 'saveManualGrid'])->name('manual.save');
                Route::post('/manual-export', [CrmSalesExcelController::class, 'exportManualGrid'])->name('manual.export');
            });

            // Backward-compatible routes for Leads import
            Route::get('leads/import', [CrmSalesExcelController::class, 'index'])->name('leads.import');
            Route::post('leads/import', [CrmSalesExcelController::class, 'import'])->name('leads.import.process');
            Route::get('leads/import/template', [CrmSalesExcelController::class, 'downloadTemplate'])->name('leads.import.template');

            // Leads
            Route::prefix('leads')->name('leads.')->group(function () {
                Route::get('/', [CrmLeadController::class, 'index'])->name('index');
                Route::get('/create', [CrmLeadController::class, 'create'])->name('create');
                Route::post('/', [CrmLeadController::class, 'store'])->name('store');
                Route::get('/export', [CrmLeadController::class, 'export'])->name('export');
                Route::get('/check-duplicates', [CrmLeadController::class, 'checkDuplicates'])->name('check-duplicates');
                Route::post('/bulk-assign', [CrmLeadController::class, 'bulkAssign'])->name('bulk-assign');
                Route::post('/bulk-status', [CrmLeadController::class, 'bulkStatus'])->name('bulk-status');
                Route::get('/{id}', [CrmLeadController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [CrmLeadController::class, 'edit'])->name('edit');
                Route::put('/{id}', [CrmLeadController::class, 'update'])->name('update');
                Route::delete('/{id}', [CrmLeadController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/convert', [CrmLeadController::class, 'convert'])->name('convert');
            });

            // Opportunities
            Route::prefix('opportunities')->name('opportunities.')->group(function () {
                Route::get('/', [CrmOpportunityController::class, 'index'])->name('index');
                Route::get('/create', [CrmOpportunityController::class, 'create'])->name('create');
                Route::post('/', [CrmOpportunityController::class, 'store'])->name('store');
                Route::get('/{id}', [CrmOpportunityController::class, 'show'])->name('show');
                Route::post('/{id}/stage', [CrmOpportunityController::class, 'updateStage'])->name('update-stage');
                Route::delete('/{id}', [CrmOpportunityController::class, 'destroy'])->name('destroy');
            });

            // Activities / Tasks
            Route::prefix('activities')->name('activities.')->group(function () {
                Route::get('/', [CrmActivityController::class, 'index'])->name('index');
                Route::get('/create', [CrmActivityController::class, 'create'])->name('create');
                Route::post('/', [CrmActivityController::class, 'store'])->name('store');
                Route::post('/{id}/complete', [CrmActivityController::class, 'complete'])->name('complete');
                Route::post('/{id}/cancel', [CrmActivityController::class, 'cancel'])->name('cancel');
                Route::delete('/{id}', [CrmActivityController::class, 'destroy'])->name('destroy');
            });

            // B2B Companies
            Route::prefix('companies')->name('companies.')->group(function () {
                Route::get('/', [CrmCompanyController::class, 'index'])->name('index');
                Route::get('/create', [CrmCompanyController::class, 'create'])->name('create');
                Route::post('/', [CrmCompanyController::class, 'store'])->name('store');
                Route::get('/{id}', [CrmCompanyController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [CrmCompanyController::class, 'edit'])->name('edit');
                Route::put('/{id}', [CrmCompanyController::class, 'update'])->name('update');
                Route::delete('/{id}', [CrmCompanyController::class, 'destroy'])->name('destroy');
            });

            // Marketing Campaigns
            Route::prefix('campaigns')->name('campaigns.')->group(function () {
                Route::get('/', [CrmCampaignController::class, 'index'])->name('index');
                Route::get('/create', [CrmCampaignController::class, 'create'])->name('create');
                Route::post('/', [CrmCampaignController::class, 'store'])->name('store');
                Route::get('/{id}', [CrmCampaignController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [CrmCampaignController::class, 'edit'])->name('edit');
                Route::put('/{id}', [CrmCampaignController::class, 'update'])->name('update');
                Route::delete('/{id}', [CrmCampaignController::class, 'destroy'])->name('destroy');
            });

            // Pipelines & Stages
            Route::prefix('pipelines')->name('pipelines.')->group(function () {
                Route::get('/', [CrmPipelineController::class, 'index'])->name('index');
                Route::post('/', [CrmPipelineController::class, 'store'])->name('store');
                Route::post('/{id}/stages', [CrmPipelineController::class, 'storeStage'])->name('stages.store');
            });

            // Dynamic Segments
            Route::prefix('segments')->name('segments.')->group(function () {
                Route::get('/', [CrmSegmentController::class, 'index'])->name('index');
                Route::get('/{id}', [CrmSegmentController::class, 'show'])->name('show');
                Route::post('/refresh', [CrmSegmentController::class, 'refresh'])->name('refresh');
            });

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [CrmReportController::class, 'index'])->name('index');
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
            Route::post('/{id}/impersonate', [UserController::class, 'impersonate'])->middleware('permission:users.view')->name('impersonate');
            Route::post('/{id}/attendance', [UserController::class, 'recordAttendance'])->middleware('permission:users.edit')->name('attendance.record');
        });

        // Impersonation Exit Route (Accessible to all authenticated users)
        Route::match(['GET', 'POST'], '/impersonate/leave', [UserController::class, 'leaveImpersonation'])->name('impersonate.leave');

        // Self-Service Hub (Attendance Check-In/Out & Leave Request Submission for all logged-in staff)
        Route::prefix('self-service')->name('self-service.')->group(function () {
            Route::get('/', [SelfServiceController::class, 'index'])->name('index');
            Route::get('/status', [SelfServiceController::class, 'status'])->name('status');
            Route::post('/check-in', [SelfServiceController::class, 'selfCheckIn'])->name('check-in');
            Route::post('/check-out', [SelfServiceController::class, 'selfCheckOut'])->name('check-out');
            Route::post('/leave', [SelfServiceController::class, 'submitLeave'])->name('leave');
        });

        // Convenient Aliases for Check-in / Check-out / Leave Requests
        Route::post('/attendance/check-in', [SelfServiceController::class, 'selfCheckIn'])->name('attendance.self-checkin');
        Route::post('/attendance/check-out', [SelfServiceController::class, 'selfCheckOut'])->name('attendance.self-checkout');
        Route::post('/leave/self-request', [SelfServiceController::class, 'submitLeave'])->name('leave.self-request');

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('index');
            Route::get('/matrix', [RoleController::class, 'matrix'])->middleware('permission:roles.view')->name('matrix');
            Route::post('/matrix/update', [RoleController::class, 'updateMatrix'])->middleware('permission:roles.edit')->name('matrix.update');
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
            Route::post('/invoice-template/reset', [SettingController::class, 'resetInvoiceTemplate'])->middleware('permission:settings.edit')->name('invoice-template.reset');
            Route::get('/invoice-template/preview', [SettingController::class, 'previewInvoiceTemplate'])->middleware('permission:settings.view')->name('invoice-template.preview');
            Route::post('/purchase-invoice-template/reset', [SettingController::class, 'resetPurchaseInvoiceTemplate'])->middleware('permission:settings.edit')->name('purchase-invoice-template.reset');
            Route::get('/purchase-invoice-template/preview', [SettingController::class, 'previewPurchaseInvoiceTemplate'])->middleware('permission:settings.view')->name('purchase-invoice-template.preview');
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

        // ==========================================
        // Human Resources Management (HR)
        // ==========================================
        Route::prefix('hr')->name('hr.')->group(function () {
            // HR Dashboard
            Route::get('/', [HrDashboardController::class, 'index'])->name('dashboard');

            // Organization
            Route::prefix('organization')->name('organization.')->group(function () {
                // Departments
                Route::get('/departments', [HrDepartmentController::class, 'index'])->name('departments.index');
                Route::post('/departments', [HrDepartmentController::class, 'store'])->name('departments.store');
                Route::put('/departments/{id}', [HrDepartmentController::class, 'update'])->name('departments.update');
                Route::delete('/departments/{id}', [HrDepartmentController::class, 'destroy'])->name('departments.destroy');

                // Positions
                Route::get('/positions', [HrPositionController::class, 'index'])->name('positions.index');
                Route::post('/positions', [HrPositionController::class, 'store'])->name('positions.store');
                Route::put('/positions/{id}', [HrPositionController::class, 'update'])->name('positions.update');
                Route::delete('/positions/{id}', [HrPositionController::class, 'destroy'])->name('positions.destroy');

                // Work Schedules
                Route::get('/work-schedules', [HrWorkScheduleController::class, 'index'])->name('work-schedules.index');
                Route::post('/work-schedules', [HrWorkScheduleController::class, 'store'])->name('work-schedules.store');
                Route::put('/work-schedules/{id}', [HrWorkScheduleController::class, 'update'])->name('work-schedules.update');
            });

            // Direct alias routes for departments
            Route::prefix('departments')->name('departments.')->group(function () {
                Route::get('/', [HrDepartmentController::class, 'index'])->name('index');
                Route::post('/', [HrDepartmentController::class, 'store'])->name('store');
                Route::put('/{id}', [HrDepartmentController::class, 'update'])->name('update');
                Route::delete('/{id}', [HrDepartmentController::class, 'destroy'])->name('destroy');
            });

            // Employees
            Route::prefix('employees')->name('employees.')->group(function () {
                Route::get('/', [HrEmployeeController::class, 'index'])->name('index');
                Route::get('/create', [HrEmployeeController::class, 'create'])->name('create');
                Route::post('/', [HrEmployeeController::class, 'store'])->name('store');
                Route::get('/{id}', [HrEmployeeController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [HrEmployeeController::class, 'edit'])->name('edit');
                Route::put('/{id}', [HrEmployeeController::class, 'update'])->name('update');
                Route::delete('/{id}', [HrEmployeeController::class, 'destroy'])->name('destroy');

                // Documents
                Route::get('/documents/all', [HrEmployeeDocumentController::class, 'index'])->name('documents.index');
                Route::post('/{employeeId}/documents', [HrEmployeeDocumentController::class, 'store'])->name('documents.store');
                Route::get('/documents/{id}/download', [HrEmployeeDocumentController::class, 'download'])->name('documents.download');
                Route::delete('/documents/{id}', [HrEmployeeDocumentController::class, 'destroy'])->name('documents.destroy');
            });

            // Recruitment
            Route::prefix('recruitment')->name('recruitment.')->group(function () {
                Route::get('/', [HrRecruitmentController::class, 'index'])->name('index');
                Route::get('/vacancies', [HrRecruitmentController::class, 'vacancies'])->name('vacancies');
                Route::post('/vacancies', [HrRecruitmentController::class, 'storeVacancy'])->name('vacancies.store');
                Route::get('/candidates', [HrRecruitmentController::class, 'candidates'])->name('candidates');
                Route::post('/candidates', [HrRecruitmentController::class, 'storeCandidate'])->name('candidates.store');
                Route::post('/candidates/{id}/convert', [HrRecruitmentController::class, 'convertCandidate'])->name('candidates.convert');
                Route::get('/interviews', [HrRecruitmentController::class, 'interviews'])->name('interviews');
                Route::post('/interviews', [HrRecruitmentController::class, 'storeInterview'])->name('interviews.store');
                Route::get('/offers', [HrRecruitmentController::class, 'offers'])->name('offers');
                Route::post('/offers', [HrRecruitmentController::class, 'storeOffer'])->name('offers.store');
            });

            // Attendance
            Route::prefix('attendance')->name('attendance.')->group(function () {
                Route::get('/', [HrAttendanceController::class, 'index'])->name('index');
                Route::get('/daily', [HrAttendanceController::class, 'daily'])->name('daily');
                Route::post('/check-in', [HrAttendanceController::class, 'checkIn'])->name('check-in');
                Route::post('/check-out', [HrAttendanceController::class, 'checkOut'])->name('check-out');
                Route::get('/overtime', [HrAttendanceController::class, 'overtime'])->name('overtime');
                Route::post('/overtime', [HrAttendanceController::class, 'storeOvertime'])->name('overtime.store');
                Route::post('/overtime/{id}/approve', [HrAttendanceController::class, 'approveOvertime'])->name('overtime.approve');
            });

            // Leave Management
            Route::prefix('leave')->name('leave.')->group(function () {
                Route::get('/requests', [HrLeaveController::class, 'requests'])->name('requests');
                Route::post('/requests', [HrLeaveController::class, 'storeRequest'])->name('requests.store');
                Route::get('/approvals', [HrLeaveController::class, 'approvals'])->name('approvals');
                Route::post('/requests/{id}/approve', [HrLeaveController::class, 'approveRequest'])->name('requests.approve');
                Route::post('/requests/{id}/reject', [HrLeaveController::class, 'rejectRequest'])->name('requests.reject');
                Route::get('/types', [HrLeaveController::class, 'types'])->name('types');
                Route::post('/types', [HrLeaveController::class, 'storeType'])->name('types.store');
                Route::get('/balances', [HrLeaveController::class, 'balances'])->name('balances');
            });

            // Payroll
            Route::prefix('payroll')->name('payroll.')->group(function () {
                Route::get('/', [HrPayrollController::class, 'index'])->name('index');
                Route::get('/periods', [HrPayrollController::class, 'periods'])->name('periods');
                Route::post('/periods', [HrPayrollController::class, 'storePeriod'])->name('periods.store');
                Route::post('/periods/{id}/generate', [HrPayrollController::class, 'generate'])->name('periods.generate');
                Route::post('/periods/{id}/finalize', [HrPayrollController::class, 'finalize'])->name('periods.finalize');
                Route::get('/payslips', [HrPayrollController::class, 'payslips'])->name('payslips');
                Route::get('/payslips/{id}', [HrPayrollController::class, 'showPayslip'])->name('payslips.show');
                Route::get('/structures', [HrPayrollController::class, 'structures'])->name('structures');
                Route::get('/advances', [HrPayrollController::class, 'advances'])->name('advances');
                Route::post('/advances', [HrPayrollController::class, 'storeAdvance'])->name('advances.store');
                Route::get('/loans', [HrPayrollController::class, 'loans'])->name('loans');
                Route::post('/loans', [HrPayrollController::class, 'storeLoan'])->name('loans.store');
            });

            // Performance
            Route::prefix('performance')->name('performance.')->group(function () {
                Route::get('/reviews', [HrPerformanceController::class, 'reviews'])->name('reviews');
                Route::get('/goals', [HrPerformanceController::class, 'goals'])->name('goals');
                Route::post('/goals', [HrPerformanceController::class, 'storeGoal'])->name('goals.store');
                Route::get('/kpis', [HrPerformanceController::class, 'kpis'])->name('kpis');
                Route::get('/promotions', [HrPerformanceController::class, 'promotions'])->name('promotions');
                Route::post('/promotions', [HrPerformanceController::class, 'storePromotion'])->name('promotions.store');
            });

            // Training
            Route::prefix('training')->name('training.')->group(function () {
                Route::get('/programs', [HrTrainingController::class, 'programs'])->name('programs');
                Route::post('/programs', [HrTrainingController::class, 'storeProgram'])->name('programs.store');
                Route::get('/employee-training', [HrTrainingController::class, 'employeeTraining'])->name('employee-training');
                Route::post('/employee-training', [HrTrainingController::class, 'assignEmployee'])->name('employee-training.assign');
                Route::get('/certificates', [HrTrainingController::class, 'certificates'])->name('certificates');
            });

            // Employee Requests
            Route::prefix('requests')->name('requests.')->group(function () {
                Route::get('/', [HrEmployeeRequestController::class, 'index'])->name('index');
                Route::post('/', [HrEmployeeRequestController::class, 'storeRequest'])->name('store');
                Route::put('/{id}/status', [HrEmployeeRequestController::class, 'updateStatus'])->name('update-status');
                Route::get('/complaints', [HrEmployeeRequestController::class, 'complaints'])->name('complaints');
                Route::get('/suggestions', [HrEmployeeRequestController::class, 'suggestions'])->name('suggestions');
                Route::get('/transfers', [HrEmployeeRequestController::class, 'transfers'])->name('transfers');
                Route::post('/transfers', [HrEmployeeRequestController::class, 'storeTransfer'])->name('transfers.store');
                Route::get('/disciplinary', [HrEmployeeRequestController::class, 'disciplinary'])->name('disciplinary');
                Route::post('/disciplinary', [HrEmployeeRequestController::class, 'storeDisciplinary'])->name('disciplinary.store');
            });

            // Assets
            Route::prefix('assets')->name('assets.')->group(function () {
                Route::get('/', [HrAssetController::class, 'index'])->name('index');
                Route::post('/', [HrAssetController::class, 'store'])->name('store');
                Route::get('/assignments', [HrAssetController::class, 'assignments'])->name('assignments');
                Route::post('/assign', [HrAssetController::class, 'assign'])->name('assign');
                Route::post('/assignments/{id}/return', [HrAssetController::class, 'returnAsset'])->name('return');
                Route::get('/returns', [HrAssetController::class, 'returns'])->name('returns');
            });

            // Offboarding
            Route::prefix('offboarding')->name('offboarding.')->group(function () {
                Route::get('/resignations', [HrOffboardingController::class, 'resignations'])->name('resignations');
                Route::post('/resignations', [HrOffboardingController::class, 'storeResignation'])->name('resignations.store');
                Route::get('/terminations', [HrOffboardingController::class, 'terminations'])->name('terminations');
                Route::post('/terminations', [HrOffboardingController::class, 'storeTermination'])->name('terminations.store');
                Route::get('/exit-interviews', [HrOffboardingController::class, 'exitInterviews'])->name('exit-interviews');
                Route::post('/exit-interviews', [HrOffboardingController::class, 'storeExitInterview'])->name('exit-interviews.store');
                Route::get('/settlements', [HrOffboardingController::class, 'settlements'])->name('settlements');
                Route::post('/settlements/calculate', [HrOffboardingController::class, 'calculateSettlement'])->name('settlements.calculate');
            });

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [HrReportController::class, 'index'])->name('index');
                Route::get('/employees', [HrReportController::class, 'employees'])->name('employees');
                Route::get('/attendance', [HrReportController::class, 'attendance'])->name('attendance');
                Route::get('/leave', [HrReportController::class, 'leave'])->name('leave');
                Route::get('/payroll', [HrReportController::class, 'payroll'])->name('payroll');
                Route::get('/recruitment', [HrReportController::class, 'recruitment'])->name('recruitment');
                Route::get('/performance', [HrReportController::class, 'performance'])->name('performance');
                Route::get('/training', [HrReportController::class, 'training'])->name('training');
            });

            // Settings
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [HrSettingController::class, 'index'])->name('index');
                Route::post('/', [HrSettingController::class, 'update'])->name('update');
            });
        });

        // ==========================================
        // Supply Chain: Sourcing, QC, MES & Reports
        // ==========================================

        // Suppliers & Vendors
        Route::prefix('suppliers')->name('suppliers.')->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->middleware('permission:suppliers.view')->name('index');
            Route::get('/create', [SupplierController::class, 'create'])->middleware('permission:suppliers.create')->name('create');
            Route::post('/', [SupplierController::class, 'store'])->middleware('permission:suppliers.create')->name('store');
            Route::get('/export', [SupplierController::class, 'export'])->middleware('permission:suppliers.view')->name('export');
            Route::get('/{supplier}', [SupplierController::class, 'show'])->middleware('permission:suppliers.view')->name('show');
            Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->middleware('permission:suppliers.edit')->name('edit');
            Route::put('/{supplier}', [SupplierController::class, 'update'])->middleware('permission:suppliers.edit')->name('update');
            Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->middleware('permission:suppliers.delete')->name('destroy');
            Route::post('/{supplier}/approve', [SupplierController::class, 'approve'])->middleware('permission:suppliers.approve')->name('approve');
        });

        // Raw Material Catalog
        Route::prefix('raw-materials')->name('raw-materials.')->group(function () {
            Route::get('/', [RawMaterialController::class, 'index'])->middleware('permission:raw_materials.view')->name('index');
            Route::get('/create', [RawMaterialController::class, 'create'])->middleware('permission:raw_materials.create')->name('create');
            Route::post('/', [RawMaterialController::class, 'store'])->middleware('permission:raw_materials.create')->name('store');
            Route::get('/export', [RawMaterialController::class, 'export'])->middleware('permission:raw_materials.view')->name('export');
            Route::get('/{rawMaterial}', [RawMaterialController::class, 'show'])->middleware('permission:raw_materials.view')->name('show');
            Route::get('/{rawMaterial}/edit', [RawMaterialController::class, 'edit'])->middleware('permission:raw_materials.edit')->name('edit');
            Route::put('/{rawMaterial}', [RawMaterialController::class, 'update'])->middleware('permission:raw_materials.edit')->name('update');
            Route::delete('/{rawMaterial}', [RawMaterialController::class, 'destroy'])->middleware('permission:raw_materials.delete')->name('destroy');
        });

        // Purchase Orders
        Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
            Route::get('/', [PurchaseOrderController::class, 'index'])->middleware('permission:purchase_orders.view')->name('index');
            Route::get('/create', [PurchaseOrderController::class, 'create'])->middleware('permission:purchase_orders.create')->name('create');
            Route::post('/', [PurchaseOrderController::class, 'store'])->middleware('permission:purchase_orders.create')->name('store');
            Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->middleware('permission:purchase_orders.view')->name('show');
            Route::post('/{purchaseOrder}/submit', [PurchaseOrderController::class, 'submit'])->middleware('permission:purchase_orders.edit')->name('submit');
            Route::post('/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->middleware('permission:purchase_orders.approve')->name('approve');
            Route::post('/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->middleware('permission:purchase_orders.edit')->name('cancel');
            Route::get('/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->middleware('permission:purchase_orders.view')->name('print');
        });

        // Goods Receipt Notes (GRN)
        Route::prefix('goods-receipts')->name('goods-receipts.')->group(function () {
            Route::get('/', [GoodsReceiptController::class, 'index'])->middleware('permission:goods_receipts.view')->name('index');
            Route::get('/create', [GoodsReceiptController::class, 'create'])->middleware('permission:goods_receipts.create')->name('create');
            Route::post('/', [GoodsReceiptController::class, 'store'])->middleware('permission:goods_receipts.create')->name('store');
            Route::get('/{goodsReceipt}', [GoodsReceiptController::class, 'show'])->middleware('permission:goods_receipts.view')->name('show');
            Route::post('/{goodsReceipt}/generate-bill', [GoodsReceiptController::class, 'generateBill'])->middleware('permission:goods_receipts.create')->name('generate-bill');
        });

        // Raw Material Batches (FEFO Ledger)
        Route::prefix('batches')->name('batches.')->group(function () {
            Route::get('/', [RawMaterialBatchController::class, 'index'])->middleware('permission:batches.view')->name('index');
            Route::get('/{batch}', [RawMaterialBatchController::class, 'show'])->middleware('permission:batches.view')->name('show');
            Route::post('/{batch}/transfer', [RawMaterialBatchController::class, 'transfer'])->middleware('permission:batches.transfer')->name('transfer');
            Route::post('/{batch}/adjust', [RawMaterialBatchController::class, 'adjust'])->middleware('permission:batches.adjust')->name('adjust');
        });

        // Quality Control & Inspections
        Route::prefix('qc-inspections')->name('qc.')->group(function () {
            Route::get('/', [QcInspectionController::class, 'index'])->middleware('permission:qc.view')->name('index');
            Route::get('/create', [QcInspectionController::class, 'create'])->middleware('permission:qc.create')->name('create');
            Route::post('/', [QcInspectionController::class, 'store'])->middleware('permission:qc.create')->name('store');
            Route::get('/{qcInspection}', [QcInspectionController::class, 'show'])->middleware('permission:qc.view')->name('show');
            Route::post('/{qcInspection}/verify', [QcInspectionController::class, 'verify'])->middleware('permission:qc.verify')->name('verify');
            Route::get('/{qcInspection}/coa', [QcInspectionController::class, 'downloadCoa'])->middleware('permission:qc.view')->name('coa.download');
        });

        // Production Work Orders
        Route::prefix('work-orders')->name('work-orders.')->group(function () {
            Route::get('/', [WorkOrderController::class, 'index'])->middleware('permission:work_orders.view')->name('index');
            Route::get('/create', [WorkOrderController::class, 'create'])->middleware('permission:work_orders.create')->name('create');
            Route::post('/', [WorkOrderController::class, 'store'])->middleware('permission:work_orders.create')->name('store');
            Route::get('/{workOrder}', [WorkOrderController::class, 'show'])->middleware('permission:work_orders.view')->name('show');
            Route::post('/{workOrder}/schedule', [WorkOrderController::class, 'schedule'])->middleware('permission:work_orders.create')->name('schedule');
            Route::post('/{workOrder}/dispensing', [WorkOrderController::class, 'startDispensing'])->middleware('permission:work_orders.dispense')->name('dispensing');
            Route::post('/{workOrder}/dispense', [WorkOrderController::class, 'dispense'])->middleware('permission:work_orders.dispense')->name('dispense');
            Route::post('/{workOrder}/complete', [WorkOrderController::class, 'complete'])->middleware('permission:work_orders.complete')->name('complete');
        });

        // The 7 Mission-Critical Supply Chain Reports
        Route::prefix('reports/supply-chain')->name('supply-chain-reports.')->group(function () {
            Route::get('/vendor-scorecard', [SupplyChainReportController::class, 'vendorScorecard'])->middleware('permission:supply_chain_reports.view')->name('vendor-scorecard');
            Route::get('/valuation-expiry', [SupplyChainReportController::class, 'valuationExpiry'])->middleware('permission:supply_chain_reports.view')->name('valuation-expiry');
            Route::get('/po-aging', [SupplyChainReportController::class, 'poAging'])->middleware('permission:supply_chain_reports.view')->name('po-aging');
            Route::get('/qc-log', [SupplyChainReportController::class, 'qcLog'])->middleware('permission:supply_chain_reports.view')->name('qc-log');
            Route::get('/mrp-forecast', [SupplyChainReportController::class, 'mrpForecast'])->middleware('permission:supply_chain_reports.view')->name('mrp-forecast');
            Route::get('/scrap-variance', [SupplyChainReportController::class, 'scrapVariance'])->middleware('permission:supply_chain_reports.view')->name('scrap-variance');
            Route::get('/lot-traceability', [SupplyChainReportController::class, 'lotTraceability'])->middleware('permission:supply_chain_reports.view')->name('lot-traceability');
        });

        // Unified Corporate FINANCE Module
        Route::prefix('finance')->name('finance.')->group(function () {
            // 1. Finance Dashboard
            Route::get('/dashboard', [FinanceDashboardController::class, 'index'])->name('dashboard');

            // 2. Sales Invoices
            Route::prefix('sales-invoices')->name('sales-invoices.')->group(function () {
                Route::get('/', [SalesInvoiceController::class, 'index'])->name('index');
                Route::post('/{order}/receive-payment', [SalesInvoiceController::class, 'receivePayment'])->name('receive-payment');
            });

            // 3. Purchase Invoices (Vendor Bills)
            Route::prefix('purchase-invoices')->name('purchase-invoices.')->group(function () {
                Route::get('/', [PurchaseInvoiceController::class, 'index'])->name('index');
                Route::get('/create', [PurchaseInvoiceController::class, 'create'])->name('create');
                Route::post('/', [PurchaseInvoiceController::class, 'store'])->name('store');
                Route::get('/{purchaseInvoice}', [PurchaseInvoiceController::class, 'show'])->name('show');
                Route::get('/{purchaseInvoice}/print', [PurchaseInvoiceController::class, 'print'])->name('print');
                Route::post('/{purchaseInvoice}/pay', [PurchaseInvoiceController::class, 'pay'])->name('pay');
            });

            // 4. Collection Entries (Spreadsheet Grid & System Ingestion)
            Route::prefix('collections')->name('collections.')->group(function () {
                Route::get('/', [CollectionEntryController::class, 'index'])->name('index');
                Route::post('/', [CollectionEntryController::class, 'store'])->name('store');
                Route::post('/sync-online', [CollectionEntryController::class, 'syncOnlineSales'])->name('sync-online');
                Route::get('/export', [CollectionEntryController::class, 'export'])->name('export');
                Route::get('/{id}/voucher', [CollectionEntryController::class, 'voucher'])->name('voucher');
            });

            // 5. Customer Payments
            Route::prefix('customer-payments')->name('customer-payments.')->group(function () {
                Route::get('/', [CustomerPaymentController::class, 'index'])->name('index');
                Route::post('/', [CustomerPaymentController::class, 'store'])->name('store');
                Route::get('/{transaction}/voucher', [CustomerPaymentController::class, 'voucher'])->name('voucher');
            });

            // 5. Payment Entries (Disbursement Ledger & Supplier Payments)
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/', [PaymentEntryController::class, 'index'])->name('index');
                Route::post('/', [PaymentEntryController::class, 'store'])->name('store');
                Route::post('/{paymentEntry}/clear-cheque', [PaymentEntryController::class, 'clearCheque'])->name('clear-cheque');
                Route::get('/export', [PaymentEntryController::class, 'export'])->name('export');
                Route::get('/{id}/voucher', [PaymentEntryController::class, 'voucher'])->name('voucher');
            });
            Route::redirect('/supplier-payments', '/admin/finance/payments');

            // 6. Expenses
            Route::prefix('expenses')->name('expenses.')->group(function () {
                Route::get('/', [ExpenseController::class, 'index'])->name('index');
                Route::post('/', [ExpenseController::class, 'store'])->name('store');
            });

            // 7. Cash & Bank Accounts
            Route::prefix('accounts')->name('accounts.')->group(function () {
                Route::get('/', [FinancialAccountController::class, 'index'])->name('index');
                Route::post('/', [FinancialAccountController::class, 'store'])->name('store');
                Route::post('/transfer', [FinancialAccountController::class, 'transfer'])->name('transfer');
                Route::get('/{financialAccount}', [FinancialAccountController::class, 'show'])->name('show');
            });
        });

        // Dynamic Cascading Geo API Endpoints
        Route::get('/api/countries/{id}/cities', [CityController::class, 'getCitiesByCountry'])->name('api.countries.cities');
        Route::get('/api/cities/{id}/areas', [AreaController::class, 'getAreasByCity'])->name('api.cities.areas');
        Route::get('/api/areas/{id}/breaks', [BreakController::class, 'getBreaksByArea'])->name('api.areas.breaks');
    });
});

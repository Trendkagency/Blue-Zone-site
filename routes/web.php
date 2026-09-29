<?php

use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Dynamic Locale Switcher (supports session persistence for 'en' and 'ar')
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'], true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

// Security CAPTCHA Challenge Refresh
Route::get('/captcha/refresh', [CaptchaController::class, 'refresh'])->middleware('throttle:polling')->name('captcha.refresh');

// Payment Gateway Webhooks (Excluded from CSRF)
Route::post('/webhooks/payment/{gateway}', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
Route::post('/webhooks/simulate', [PaymentWebhookController::class, 'simulate'])->name('payment.webhook.simulate');

// FCM Device Token Registration Endpoint
Route::post('/api/fcm/register-token', [\App\Http\Controllers\Admin\NotificationController::class, 'updateFcmToken'])
    ->middleware(['web', 'throttle:polling'])
    ->name('api.fcm.register');

// Dynamic Public Geo Cascading API for Storefront & Registration
Route::get('/api/geo/countries/{id}/cities', [\App\Http\Controllers\Admin\CityController::class, 'getCitiesByCountry'])
    ->name('api.geo.cities');

// Public Order & Invoice Verification & QR Tracking Routes
Route::get('/orders/track/{order}', [\App\Http\Controllers\OrderTrackingController::class, 'track'])->name('orders.track');
Route::get('/orders/verify/{order}', [\App\Http\Controllers\OrderTrackingController::class, 'track'])->name('orders.verify');
Route::get('/invoice/verify/{order}', [\App\Http\Controllers\OrderTrackingController::class, 'track'])->name('invoice.verify');
Route::get('/orders/{order}/download-invoice', [\App\Http\Controllers\OrderTrackingController::class, 'downloadInvoice'])->name('orders.download-invoice');

// Load Customer, Admin, and MR Routes
require __DIR__ . '/customer.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/mr.php';

// Database Migration Utility Endpoint
Route::get('/migrate-database', function () {
    try {
        Artisan::call('migrate', [
            '--force' => true,
        ]);

        // Auto-clear stale caches after migration
        Artisan::call('optimize:clear');

        return response()->json([
            'success' => true,
            'message' => 'Database migrated and caches cleared successfully.',
            'output' => Artisan::output(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Migration failed.',
            'error' => $e->getMessage(),
        ], 500);
    }
});

// Cache & Optimization Clearing Utility Endpoint
Route::get('/clear-cache', function () {
    try {
        Artisan::call('optimize:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return response()->json([
            'success' => true,
            'message' => 'All caches (routes, config, views, application) cleared successfully.',
            'output' => Artisan::output(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Cache clearing failed.',
            'error' => $e->getMessage(),
        ], 500);
    }
});

// Fresh Default Products Utility Route (Truncates and puts the 4 default products with full Science Details)
Route::get('/fresh-products', function () {
    try {
        $seeder = new \Database\Seeders\ProductSeeder();
        $seeder->seedCategories();

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Models\Product::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $seeder->seedProducts();

        Artisan::call('optimize:clear');

        $products = \App\Models\Product::with('category')->get();

        return response()->json([
            'success' => true,
            'message' => 'Fresh default 4 Blue Zone products created successfully with science details.',
            'count' => $products->count(),
            'products' => $products->map(fn ($p) => [
                'id' => $p->id,
                'slug' => $p->slug,
                'sku' => $p->sku,
                'name_en' => $p->name_en,
                'name_ar' => $p->name_ar,
                'category' => $p->category?->name_en,
                'price' => $p->price,
                'science_url' => url('/science/' . $p->slug),
                'product_url' => url('/products/' . $p->slug),
            ]),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create fresh default products.',
            'error' => $e->getMessage(),
        ], 500);
    }
})->name('products.fresh');

// Seed/Update Default Products Utility Route (Updates/creates without wiping other data)
Route::get('/seed-products', function () {
    try {
        $seeder = new \Database\Seeders\ProductSeeder();
        $seeder->run();

        Artisan::call('optimize:clear');

        $products = \App\Models\Product::with('category')->get();

        return response()->json([
            'success' => true,
            'message' => 'Default 4 Blue Zone products seeded/updated successfully with science details.',
            'count' => $products->count(),
            'products' => $products->map(fn ($p) => [
                'id' => $p->id,
                'slug' => $p->slug,
                'sku' => $p->sku,
                'name_en' => $p->name_en,
                'name_ar' => $p->name_ar,
                'category' => $p->category?->name_en,
                'price' => $p->price,
                'science_url' => url('/science/' . $p->slug),
                'product_url' => url('/products/' . $p->slug),
            ]),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to seed default products.',
            'error' => $e->getMessage(),
        ], 500);
    }
})->name('products.seed');
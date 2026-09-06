<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

// Dynamic Locale Switcher (supports session persistence for 'en' and 'ar')
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'], true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

// Payment Gateway Webhooks (Excluded from CSRF)
Route::post('/webhooks/payment/{gateway}', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
Route::post('/webhooks/simulate', [PaymentWebhookController::class, 'simulate'])->name('payment.webhook.simulate');

// Load Customer and Admin Routes
require __DIR__.'/customer.php';
require __DIR__.'/admin.php';

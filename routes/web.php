<?php

use Illuminate\Support\Facades\Route;

// Dynamic Locale Switcher (supports session persistence for 'en' and 'ar')
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'], true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

// Load Customer and Admin Routes
require __DIR__.'/customer.php';
require __DIR__.'/admin.php';

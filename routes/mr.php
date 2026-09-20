<?php

use App\Http\Controllers\Mr\MrDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('mr')->name('mr.')->middleware(['web', 'auth:web'])->group(function () {
    Route::get('/', [MrDashboardController::class, 'index'])->name('dashboard');
    Route::post('/checkin', [MrDashboardController::class, 'checkIn'])->middleware('throttle:30,1')->name('checkin');
    Route::post('/checkout', [MrDashboardController::class, 'checkOut'])->middleware('throttle:30,1')->name('checkout');
    Route::post('/schedule', [MrDashboardController::class, 'schedule'])->name('schedule');
    Route::get('/calendar-events', [MrDashboardController::class, 'calendarEvents'])->name('calendar.events');
});

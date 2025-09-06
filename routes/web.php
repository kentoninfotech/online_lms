<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ZoomWebhookController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RescheduleController;


// public endpoint for Zoom to POST webhooks to
Route::post('/webhooks/zoom', [ZoomWebhookController::class, 'handle']);

Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    // manual finalize attendance for occurrence
    Route::post('/occurrences/{occurrence}/finalize', [AttendanceController::class, 'finalize'])
        ->name('admin.occurrences.finalize');
});

// Reschedule routes
Route::post('/occurrences/{occurrence}/reschedule', [RescheduleController::class, 'store'])
    ->name('reschedule.store');
Route::post('/reschedules/{reschedule}/approve', [RescheduleController::class, 'approve'])
    ->name('reschedule.approve');
Route::post('/reschedules/{reschedule}/reject', [RescheduleController::class, 'reject'])
    ->name('reschedule.reject');


Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

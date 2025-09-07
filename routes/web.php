<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ZoomWebhookController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RescheduleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;


// public endpoint for Zoom to POST webhooks to
Route::post('/webhooks/zoom', [ZoomWebhookController::class, 'handle']);

Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    // manual finalize attendance for occurrence
    Route::post('/occurrences/{occurrence}/finalize', [AttendanceController::class, 'finalize'])->name('admin.occurrences.finalize');
    // payment approval/rejection
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
});

// Reschedule routes
Route::post('/occurrences/{occurrence}/reschedule', [RescheduleController::class, 'store'])->name('reschedule.store');
Route::post('/reschedules/{reschedule}/approve', [RescheduleController::class, 'approve'])->name('reschedule.approve');
Route::post('/reschedules/{reschedule}/reject', [RescheduleController::class, 'reject'])->name('reschedule.reject');
// Subscription and Payment routes
Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
Route::post('/payments/upload', [PaymentController::class, 'uploadEvidence'])->name('payments.upload');


Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ZoomWebhookController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RescheduleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Dashboard\StudentDashboardController;
use App\Http\Controllers\Student\StudentLessonController;
use App\Http\Controllers\Student\StudentAttendanceController;
use App\Http\Controllers\Student\StudentNotificationController;


// public endpoint for Zoom to POST webhooks to
Route::post('/webhooks/zoom', [ZoomWebhookController::class, 'handle']);

// ADMIN ROUTES
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

// STUDENT ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/lessons', [StudentLessonController::class, 'lessons'])->name('student.lessons');
    Route::get('/student/attendance', [StudentAttendanceController::class, 'attendance'])->name('student.attendance');
    // FIX/CREATE ROUTE CONTROLLER METHOD
    Route::get('/student/settings', [StudentLessonController::class, 'settings'])->name('student.settings');
    Route::get('/student/notifications', [StudentNotificationController::class, 'notifications'])->name('student.notifications');
    Route::post('/student/notifications/{id}/read', [StudentNotificationController::class, 'markAsRead'])->name('student.notifications.read');
    Route::post('notifications/read-all', [StudentNotificationController::class, 'markAllRead'])->name('student.notifications.readAll');
});


Auth::routes();

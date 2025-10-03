<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ZoomWebhookController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RescheduleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ZoomController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\LinkStudentParentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Dashboard\StudentDashboardController;
use App\Http\Controllers\Student\StudentLessonController;
use App\Http\Controllers\Student\StudentAttendanceController;
use App\Http\Controllers\Student\StudentNotificationController;
use App\Http\Controllers\Dashboard\InstructorDashboardController;
use App\Http\Controllers\Instructor\InstructorLessonController;
use App\Http\Controllers\Instructor\InstructorStudentController;
use App\Http\Controllers\Dashboard\ParentDashboardController;


// public endpoint for Zoom to POST webhooks to
Route::post('/webhooks/zoom', [ZoomWebhookController::class, 'handle']);

// ADMIN ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/students', [AdminDashboardController::class, 'students'])->name('admin.students');
    Route::get('/admin/instructors', [AdminDashboardController::class, 'instructors'])->name('admin.instructors');
    Route::get('/admin/parents', [AdminDashboardController::class, 'parents'])->name('admin.parents');
    Route::get('/admin/lessons', [LessonController::class, 'lessons'])->name('admin.lessons');
    Route::get('/admin/subscriptions', [AdminDashboardController::class, 'subscriptions'])->name('admin.subscriptions');
    Route::get('/admin/payments', [AdminDashboardController::class, 'payments'])->name('admin.payments');
    Route::get('/admin/reschedules', [AdminDashboardController::class, 'reschedules'])->name('admin.reschedules');
    // REMEMBER TO CHANGE/MAKE NOTIFICATION DRY
    // Route::get('/admin/notifications', [InstructorNotificationController::class, 'notifications'])->name('admin.notifications');
    // Route::post('/admin/notifications/{id}/read', [InstructorNotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    // Route::post('notifications/read-all', [InstructorNotificationController::class, 'markAllRead'])->name('admin.notifications.readAll');
    Route::get('/admin/settings', [InstructorLessonController::class, 'settings'])->name('admin.settings');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    // manual finalize attendance for occurrence
    Route::post('/occurrences/{occurrence}/finalize', [AttendanceController::class, 'finalize'])->name('admin.occurrences.finalize');
    // payment approval/rejection
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
});


Route::middleware(['auth'])->group(function () {
    // GLOBAL ROUTES
    Route:: get('/student/{student}', [StudentController::class, 'show'])->name('show.student');
    Route:: get('/parent/{parent}', [ParentController::class, 'show'])->name('show.parent');
    Route:: get('/instructor/{instructor}', [InstructorController::class, 'show'])->name('show.instructor');
    // USER ROUTE
    Route::get('/users/{user}/edit/{role}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}/update/{role}', [UserController::class, 'update'])->name('users.update');
    // My Profile
    Route::get('/my-profile', [ProfileController::class, 'show'])->name('my.profile');
    Route::put('/users/{user}/change-password', [ProfileController::class, 'updatePassword'])->name('profile.change.password');
    Route::put('/users/{user}/upload-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.upload.picture');

    // NOTIFICATION ROUTES
    Route::get('/notifications', [NotificationController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read.all');
    // Export routes
    Route::get('/students/{student}/lessons/export/{format}', [StudentController::class, 'exportLessons'])->name('students.lessons.export');
    Route::get('/students/{student}/attendance/export/{format}', [StudentController::class, 'exportAttendance'])->name('students.attendance.export');

    // LESSON ROUTE
    Route::post('/lesson', [LessonController::class, 'store'])->name('lesson.store');
    // ZOOM MEETING
    Route::post('/zoom/{occurrence}/add', [ZoomController::class, 'addZoom'])->name('add.zoom');

    // Reschedule routes
    Route::post('/reschedules/{occurrence}/request', [RescheduleController::class, 'store'])->name('reschedule.store');
    Route::post('/reschedules/{reschedule}/approve', [RescheduleController::class, 'approve'])->name('reschedule.approve');
    Route::post('/reschedules/{reschedule}/reject', [RescheduleController::class, 'reject'])->name('reschedule.reject');
    // Subscription and Payment routes
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::post('/payments/upload', [PaymentController::class, 'uploadEvidence'])->name('payments.upload');

});

// STUDENT ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::post('/generate-link-code', [LinkStudentParentController::class, 'generateLinkCode'])->name('generate.link.code');
    Route::get('/my-lessons', [StudentLessonController::class, 'lessons'])->name('student.lessons');
    Route::get('/my-attendance', [StudentAttendanceController::class, 'attendance'])->name('student.attendance');
    // FIX/CREATE ROUTE CONTROLLER METHOD
    Route::get('/student/settings', [StudentLessonController::class, 'settings'])->name('student.settings');
});

// INSTRUCTOR ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/instructor', [InstructorDashboardController::class, 'index'])->name('instructor.dashboard');
    Route::get('/instructor/lessons', [InstructorLessonController::class, 'lessons'])->name('instructor.lessons');
    Route::get('/instructor/students', [InstructorStudentController::class, 'students'])->name('instructor.students');
    Route::get('/instructor/reschedules', [InstructorStudentController::class, 'reschedules'])->name('instructor.reschedules');
    // FIX/CREATE ROUTE CONTROLLER METHOD
    Route::get('/instructor/settings', [InstructorLessonController::class, 'settings'])->name('instructor.settings');
});

// PARENT ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/parent', [ParentDashboardController::class, 'index'])->name('parent.dashboard');
    Route::get('/children', [ParentDashboardController::class, 'children'])->name('parent.children');
    Route::get('/lessons', [ParentDashboardController::class, 'upcoming'])->name('parent.lessons');
    Route::get('/reschedules', [ParentDashboardController::class, 'reschedules'])->name('parent.reschedules');
    Route::get('/payments', [ParentDashboardController::class, 'payments'])->name('parent.payments');
    Route::post('/link-child', [LinkStudentParentController::class, 'linkChild'])->name('parent.link.child');
    // Route::get('/parent/lessons', [InstructorLessonController::class, 'lessons'])->name('parent.lessons');
    // FIX/CREATE ROUTE CONTROLLER METHOD
    // Route::get('/parent/settings', [InstructorLessonController::class, 'settings'])->name('parent.settings');
});


Auth::routes();

Route::get('/', function() {
    return view('auth.login');
});

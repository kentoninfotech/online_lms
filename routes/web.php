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
use App\Http\Controllers\JoinClassController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\LinkStudentParentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BulkMessageController;
use App\Http\Controllers\Dashboard\StudentDashboardController;
use App\Http\Controllers\Student\StudentLessonController;
use App\Http\Controllers\Student\StudentAttendanceController;
use App\Http\Controllers\Student\StudentNotificationController;
use App\Http\Controllers\Dashboard\InstructorDashboardController;
use App\Http\Controllers\Instructor\InstructorLessonController;
use App\Http\Controllers\Instructor\InstructorStudentController;
use App\Http\Controllers\Dashboard\ParentDashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\CoursePaymentController;
use App\Http\Controllers\CourseBulkMessageController;
use App\Http\Controllers\CourseContentController;
use App\Http\Controllers\CourseQuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\QuizSubmissionController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseImportController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\LiveSessionController;
use App\Http\Controllers\CourseDiscussionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\Admin\HomepageSettingController;
use App\Http\Controllers\Admin\SiteBuilderController;
use App\Http\Controllers\ContactMessageController;
use Illuminate\Support\Facades\Artisan;

// MAIL TEST ROUTE
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendBulkMessageJob;

Route::get('/mail-test', function () {
    // try {
    //     Mail::raw('Test mail from Gmail SMTP setup', function ($m) {
    //         $m->to('dbillionmindset@gmail.com')->subject('Laravel Mail Test');
    //     });
    //     return 'Mail sent successfully ✅';
    // } catch (\Throwable $e) {
    //     return 'Mail failed ❌: ' . $e->getMessage();
    // }

    $user = App\Models\User::find(4);
    $user->notify(new App\Notifications\BulkMessageNotification('Direct Test', 'This is a direct email', ['mail']));
    echo "✅ Mail sent!";
    // return 'Mail sent successfully ✅';
    // SendBulkMessageJob::dispatch(1);
    // echo "✅ Job dispatched.";

});

// public endpoint for Zoom to POST webhooks to
Route::post('/webhooks/zoom', [ZoomWebhookController::class, 'handle']);

// ------------------------------
// GLOBAL ROUTES
// Require both authentication AND email verification for all auth routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route:: get('/student/{student}', [StudentController::class, 'show'])->name('show.student');
    Route:: get('/parent/{parent}', [ParentController::class, 'show'])->name('show.parent');
    // Tutor routes - more specific routes must come before parameterized ones
    Route::get('/tutor/my-courses', [InstructorDashboardController::class, 'myCourses'])->middleware('role:instructor')->name('tutor.my-courses');
    Route:: get('/instructor/{instructor}', [InstructorController::class, 'show'])->name('show.instructor');
    // USER ROUTE
    Route::get('/users/{user}/edit/{role}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}/update/{role}', [UserController::class, 'update'])->name('users.update');
    // My Profile
    Route::get('/my-profile', [ProfileController::class, 'show'])->name('my.profile');
    Route::put('/users/{user}/change-password', [ProfileController::class, 'updatePassword'])->name('profile.change.password');
    Route::put('/users/{user}/upload-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.upload.picture');
    // Add Subscription
    Route::get('/subscriptions/{student}', [SubscriptionController::class, 'create'])->name('subscription.create');
    Route::post('/subscriptions/{student}/{plan}/store', [SubscriptionController::class, 'store'])->name('subscription.store');
    // LESSON ROUTE
    Route::get('/lesson/add', [LessonController::class, 'create'])->name('lesson.create');
    Route::get('/lesson/{lesson}/edit', [LessonController::class, 'edit'])->name('lesson.edit');
    Route::post('/lesson', [LessonController::class, 'store'])->name('lesson.store');
    Route::put('/lesson/{lesson}/update', [LessonController::class, 'update'])->name('lesson.update');
    Route::delete('/lesson/{lesson}/delete', [LessonController::class, 'delete'])->name('lesson.delete');
    // JOIN CLASS ROUTE
    Route::get('/lesson/{occurrence}/join', [JoinClassController::class, 'join'])->name('lesson.join');
    Route::get('/lesson/{occurrence}/waiting', [JoinClassController::class, 'waiting'])->name('lesson.waiting');
    // CALENDAR ROUTE
    Route::get('/calendar/occurrences', [CalendarController::class, 'fetchEvents'])->name('calendar.occurrences');
   
    // NOTIFICATION ROUTES
    Route::get('/notifications', [NotificationController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read.all');
    // Export routes
    Route::get('/students/{student}/lessons/export/{format}', [StudentController::class, 'exportLessons'])->name('students.lessons.export');
    Route::get('/students/{student}/attendance/export/{format}', [StudentController::class, 'exportAttendance'])->name('students.attendance.export');

    // BULK MESSAGE ROUTES
    Route::get('/bulk-messages', [BulkMessageController::class, 'index'])->name('bulk-messages.index');
    Route::get('/bulk-messages/logs', [BulkMessageController::class, 'logs'])->name('bulk-messages.logs');
    Route::get('/bulk-messages/{id}/recipients', [BulkMessageController::class, 'recipients'])->name('bulk-messages.recipients');
    Route::get('/bulk-messages/fetch', [BulkMessageController::class, 'fetchRecipients'])->name('bulk-messages.fetch');
    Route::post('/bulk-messages/send', [BulkMessageController::class, 'send'])->name('bulk-messages.send');

    // ZOOM MEETING
    Route::post('/zoom/{occurrence}/add', [ZoomController::class, 'addZoom'])->name('add.zoom');

    // Reschedule routes
    Route::post('/reschedules/{occurrence}/request', [RescheduleController::class, 'store'])->name('reschedule.store');
    Route::post('/reschedules/{reschedule}/approve', [RescheduleController::class, 'approve'])->name('reschedule.approve');
    Route::post('/reschedules/{reschedule}/reject', [RescheduleController::class, 'reject'])->name('reschedule.reject');
    
    // Subscription and Payment routes
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/upload', [PaymentController::class, 'uploadEvidence'])->name('payments.upload');
    Route::get('/payments/{parent}/student-sub', [PaymentController::class, 'getParentStudentSubscription'])->name('parents.student.subscriptions');

});

// ------------------------------
// ADMIN ROUTES
// ------------------------------
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/students', [AdminDashboardController::class, 'students'])->name('admin.students');
    Route::get('/admin/instructors', [AdminDashboardController::class, 'instructors'])->name('admin.instructors');
    Route::get('/admin/parents', [AdminDashboardController::class, 'parents'])->name('admin.parents');
    Route::get('/admin/lessons', [LessonController::class, 'lessons'])->name('admin.lessons');
    Route::get('/admin/payments', [AdminDashboardController::class, 'payments'])->name('admin.payments');
    Route::get('/admin/reschedules', [AdminDashboardController::class, 'reschedules'])->name('admin.reschedules');
    Route::get('/admin/subscriptions', [AdminDashboardController::class, 'subscriptions'])->name('admin.subscriptions');
    Route::post('/admin/subscriptions/{subscription}/active', [SubscriptionController::class, 'activate'])->name('subscriptions.activate');
    Route::post('/admin/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::patch('/admin/subscriptions/{subscription}/update-dates', [SubscriptionController::class, 'updateDates'])->name('subscriptions.update-dates');
    Route::get('/admin/plans', [PlanController::class, 'plans'])->name('admin.plans');
    Route::post('/admin/plans/create', [PlanController::class, 'create'])->name('plan.create');
    Route::put('/admin/plans/{plan}', [PlanController::class, 'update'])->name('plan.update');
    Route::delete('/admin/plans/{plan}/delete', [PlanController::class, 'destroy'])->name('plan.destroy');
    Route::get('create/{role}', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('store/{role}', [UserController::class, 'store'])->name('admin.users.store');
    Route::delete('user/{user}/delete', [UserController::class, 'delete'])->name('admin.users.delete');
    Route::get('/admin/settings', [InstructorLessonController::class, 'settings'])->name('admin.settings');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    // manual finalize attendance for occurrence
    Route::get('/attendance/{occurrence}/finalize', [AttendanceController::class, 'finalize'])->name('admin.attendance.finalize');
    // Fix course dates and venues
    Route::post('/admin/fix-course-dates-venues', [AdminDashboardController::class, 'fixCourseDatesAndVenues'])->name('admin.fix-course-dates-venues');
    // payment approval/rejection
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

    // Admin Accounts Management
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminAccountController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AdminAccountController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AdminAccountController::class, 'store'])->name('store');
        Route::get('/{admin}/edit', [\App\Http\Controllers\Admin\AdminAccountController::class, 'edit'])->name('edit');
        Route::put('/{admin}', [\App\Http\Controllers\Admin\AdminAccountController::class, 'update'])->name('update');
        Route::delete('/{admin}', [\App\Http\Controllers\Admin\AdminAccountController::class, 'destroy'])->name('destroy');
    });
});

// ------------------------------
//  STUDENT ROUTES
// ------------------------------
Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::post('/generate-link-code', [LinkStudentParentController::class, 'generateLinkCode'])->name('generate.link.code');
    Route::get('/my-lessons', [StudentLessonController::class, 'lessons'])->name('student.lessons');
    Route::get('/my-attendance', [StudentAttendanceController::class, 'attendance'])->name('student.attendance');
    // FIX/CREATE ROUTE CONTROLLER METHOD
    Route::get('/student/settings', [StudentLessonController::class, 'settings'])->name('student.settings');

    // Student Quiz Routes
    Route::get('/courses/{course}/quiz/{quiz}/take', [StudentQuizController::class, 'take'])->name('student.quiz.take');
    Route::post('/courses/{course}/quiz/{quiz}/submit', [StudentQuizController::class, 'submit'])->name('student.quiz.submit');
    Route::get('/courses/{course}/quiz/{quiz}/results/{submission}', [StudentQuizController::class, 'results'])->name('student.quiz.results');

    // Certificate Routes
    Route::get('/courses/{course}/certificate', [CertificateController::class, 'view'])->name('student.certificate.view');
    Route::get('/courses/{course}/certificate/download/{submission?}', [CertificateController::class, 'download'])->name('student.certificate.download');
    Route::post('/courses/{course}/certificate/mark-complete', [CertificateController::class, 'markComplete'])->name('student.certificate.mark-complete');
});

// ------------------------------
// INSTRUCTOR ROUTES
// ------------------------------
Route::middleware(['auth', 'verified', 'role:instructor'])->group(function () {
    Route::get('/dashboard/instructor', [InstructorDashboardController::class, 'index'])->name('instructor.dashboard');
    Route::get('/instructors/lessons', [InstructorLessonController::class, 'lessons'])->name('instructor.lessons');
    Route::get('/instructors/students', [InstructorStudentController::class, 'students'])->name('instructor.students');
    Route::get('/instructors/reschedules', [InstructorStudentController::class, 'reschedules'])->name('instructor.reschedules');
    // FIX/CREATE ROUTE CONTROLLER METHOD
    Route::get('/instructor/settings', [InstructorLessonController::class, 'settings'])->name('instructor.settings');
});

// ------------------------------
// PARENT ROUTES
// ------------------------------
Route::middleware(['auth', 'verified', 'role:parent'])->group(function () {
    Route::get('/dashboard/parent', [ParentDashboardController::class, 'index'])->name('parent.dashboard');
    Route::get('/children', [ParentDashboardController::class, 'children'])->name('parent.children');
    Route::get('/lessons', [ParentDashboardController::class, 'upcoming'])->name('parent.lessons');
    Route::get('/reschedules', [ParentDashboardController::class, 'reschedules'])->name('parent.reschedules');
    Route::get('/payments', [ParentDashboardController::class, 'payments'])->name('parent.payments');
    Route::get('/payments/upload', [PaymentController::class, 'uploadEvidence'])->name('payment.upload');
    Route::post('/link-child', [LinkStudentParentController::class, 'linkChild'])->name('parent.link.child');
    // Route::get('/parent/lessons', [InstructorLessonController::class, 'lessons'])->name('parent.lessons');
    // FIX/CREATE ROUTE CONTROLLER METHOD
    // Route::get('/parent/settings', [InstructorLessonController::class, 'settings'])->name('parent.settings');
});

Auth::routes(['verify' => true]);

// Public email resend route (for users who haven't verified yet)
Route::post('/email/public-resend', [\App\Http\Controllers\Auth\PublicEmailResendController::class, 'resend'])->name('email.public-resend');

Route::get('/', function() {
    // Check if user is authenticated
    if (!auth()->check()) {
        return view('auth.login');
    }
    
    $user = auth()->user();
    
    // Redirect based on user type
    if ($user->user_type === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->user_type === 'instructor') {
        return redirect()->route('instructor.dashboard');
    } elseif ($user->user_type === 'student') {
        return redirect()->route('student.dashboard');
    } elseif ($user->user_type === 'parent') {
        return redirect()->route('parent.dashboard');
    } else {
        return redirect()->route('courses.index'); // Fallback if user_type is undefined
    }
});

Route::get('/artisan/{secret}/{command}', function ($secret, $command) {
    // ✅ Set your secret key here
    $validSecret = 'va12345artisan';

    if ($secret !== $validSecret) {
        abort(403, 'Unauthorized access');
    }

    try {
        Artisan::call($command);
        return "✅ Command '{$command}' executed successfully: " . Artisan::output();
    } catch (\Exception $e) {
        return "❌ Error executing command '{$command}': " . $e->getMessage();
    }
});

// Attendance routes for instructor dashboard
Route::middleware(['auth'])->group(function () {
    Route::patch('/attendance/{attendance}/status', [AttendanceController::class, 'updateStatus'])->name('attendance.update-status');
    Route::post('/attendance/{attendance}/report', [AttendanceController::class, 'saveReport'])->name('attendance.save-report');
    Route::get('/attendance/{attendance}/report', [AttendanceController::class, 'getReport'])->name('attendance.get-report');
});

// ========================================
// PUBLIC COURSE ROUTES
// ========================================
Route::get('/', [CourseController::class, 'index'])->name('courses.index');
Route::get('/all-courses', [CourseController::class, 'allCourses'])->name('courses.all');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.landing');
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');
Route::get('/courses/level/{level}/category/{category}', [CourseController::class, 'byLevelCategory'])->name('courses.by-level-category');
Route::get('/courses/category/{category}', [CourseController::class, 'byCategory'])->name('courses.by-category');
Route::get('/course/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/facilitators/{facilitator}', [FacilitatorController::class, 'show'])->name('facilitators.show');

// Services and Gallery (Public)
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/service/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::post('/service/{service}/request', [ServiceRequestController::class, 'store'])->name('service.request.store');

Route::get('/galleries', [GalleryController::class, 'index'])->name('galleries.index');
Route::get('/gallery/{gallery:slug}', [GalleryController::class, 'show'])->name('galleries.show');

// Contact Form (Public)
Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

// ========================================
// AUTHENTICATED COURSE ROUTES
// ========================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Enrollment
    Route::get('/course/{course}/enroll', [CourseEnrollmentController::class, 'create'])->name('courses.enroll');
    Route::post('/course/{course}/enroll', [CourseEnrollmentController::class, 'store'])->name('courses.enroll.store');
    Route::get('/my-enrollments', [CourseEnrollmentController::class, 'myEnrollments'])->name('courses.my-enrollments');

    // Course Payments
    Route::get('/course-payment/{payment}', [CoursePaymentController::class, 'showPaymentMethods'])->name('course.payment.show');
    Route::get('/course-payment/{payment}/paystack', [CoursePaymentController::class, 'payWithPaystack'])->name('course.payment.paystack');
    Route::get('/course-payment/{payment}/bank', [CoursePaymentController::class, 'payWithBank'])->name('course.payment.bank');
    Route::post('/course-payment/{payment}/upload-evidence', [CoursePaymentController::class, 'uploadEvidence'])->name('course.payment.upload-evidence');
    Route::get('/course-payment/{payment}/pending', [CoursePaymentController::class, 'showPendingStatus'])->name('course.payment.pending');

    // Course Learning
    Route::get('/course/{course}/learn', [CourseContentController::class, 'index'])->name('courses.learn');
    Route::get('/course/{course}/content/{content}', [CourseContentController::class, 'show'])->name('courses.learn.content');
    Route::post('/course/{course}/content/{content}/complete', [CourseContentController::class, 'markComplete'])->name('courses.learn.content.complete');

    // Quizzes
    Route::get('/course/{course}/quiz/{quiz}', [CourseQuizController::class, 'show'])->name('courses.learn.quiz');
    Route::post('/course/{course}/quiz/{quiz}/submit', [CourseQuizController::class, 'submit'])->name('courses.learn.quiz.submit');
    Route::get('/course/{course}/quiz/{quiz}/result/{submission}', [CourseQuizController::class, 'result'])->name('courses.learn.quiz-result');

    // Discussions
    Route::get('/course/{course}/discussions', [CourseDiscussionController::class, 'index'])->name('courses.discussions.index');
    Route::get('/course/{course}/discussions/create', [CourseDiscussionController::class, 'create'])->name('courses.discussions.create');
    Route::post('/course/{course}/discussions', [CourseDiscussionController::class, 'store'])->name('courses.discussions.store');
    Route::get('/course/{course}/discussions/{discussion}', [CourseDiscussionController::class, 'show'])->name('courses.discussions.show');
    Route::post('/course/{course}/discussions/{discussion}/reply', [CourseDiscussionController::class, 'reply'])->name('courses.discussions.reply');

    // Live Sessions
    Route::get('/live-sessions/upcoming', [LiveSessionController::class, 'upcomingSessions'])->name('courses.upcoming-sessions');
    Route::get('/course/{course}/live-session/{session}', [LiveSessionController::class, 'show'])->name('courses.live-session');

    // Certificates
    Route::get('/course/{course}/certificate/generate/{enrollment}', [CertificateController::class, 'generate'])->name('courses.certificate.generate');
    Route::get('/course/{course}/certificate/download/{enrollment}', [CertificateController::class, 'downloadCertificate'])->name('courses.certificate.download');

    // Course Bulk Messaging (for tutors and admins)
    Route::get('/course/{course}/announcement/create', [CourseBulkMessageController::class, 'create'])->name('course.announcement.create');
    Route::post('/course/{course}/announcement', [CourseBulkMessageController::class, 'store'])->name('course.announcement.store');
    Route::get('/course/{course}/announcement/history', [CourseBulkMessageController::class, 'history'])->name('course.announcement.history');
    Route::get('/announcement/{message}', [CourseBulkMessageController::class, 'show'])->name('course.announcement.show');
    Route::post('/announcement/{message}/send', [CourseBulkMessageController::class, 'send'])->name('course.announcement.send');
});

// ========================================
// ADMIN COURSE MANAGEMENT ROUTES
// ========================================
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // ========== LEARNING CONTENT ==========
    Route::get('/learning-content', [CourseContentController::class, 'adminListAll'])->name('learning-content.index');
    Route::get('/learning-content/{content}', [CourseContentController::class, 'adminViewContent'])->name('learning-content.show');
    
    // ========== QUIZZES ==========
    Route::get('/quizzes', [CourseQuizController::class, 'adminListAll'])->name('quizzes.index');
    Route::get('/quizzes/{quiz}', [CourseQuizController::class, 'adminViewQuiz'])->name('quizzes.show');
    Route::get('/quiz-submissions', [CourseQuizController::class, 'adminListSubmissions'])->name('quiz-submissions.index');
    
    // ========== LIVE SESSIONS ==========
    Route::get('/live-sessions-all', [LiveSessionController::class, 'adminListAll'])->name('live-sessions-all.index');
    
    // ========== COURSE Management
    Route::get('/courses', [CourseController::class, 'adminIndex'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'adminCreate'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'adminStore'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'adminShow'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'adminEdit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'adminUpdate'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'adminDestroy'])->name('courses.destroy');
    Route::post('/courses/{course}/generate-venues', [CourseController::class, 'generateVenuesForCourse'])->name('courses.generate-venues');

    // Course Bulk Import
    Route::get('/courses/import/form', [CourseImportController::class, 'create'])->name('courses.import');
    Route::post('/courses/import', [CourseImportController::class, 'store'])->name('courses.import.store');

    // AI Content Generator Routes
    Route::prefix('ai-content-generator')->name('ai-content-generator.')->group(function () {
        Route::post('/generate-overview', [\App\Http\Controllers\Admin\AIContentGeneratorController::class, 'generateOverview'])->name('generate-overview');
        Route::post('/generate-outline', [\App\Http\Controllers\Admin\AIContentGeneratorController::class, 'generateOutline'])->name('generate-outline');
        Route::post('/generate-content', [\App\Http\Controllers\Admin\AIContentGeneratorController::class, 'generateContent'])->name('generate-content');
        Route::get('/providers', [\App\Http\Controllers\Admin\AIContentGeneratorController::class, 'getProviders'])->name('providers');
    });

    // Course Categories
    Route::get('/course-categories', [CourseCategoryController::class, 'adminIndex'])->name('course-categories.index');
    Route::get('/course-categories/create', [CourseCategoryController::class, 'adminCreate'])->name('course-categories.create');
    Route::post('/course-categories', [CourseCategoryController::class, 'adminStore'])->name('course-categories.store');
    Route::get('/course-categories/{category}/edit', [CourseCategoryController::class, 'adminEdit'])->name('course-categories.edit');
    Route::put('/course-categories/{category}', [CourseCategoryController::class, 'adminUpdate'])->name('course-categories.update');
    Route::delete('/course-categories/{category}', [CourseCategoryController::class, 'adminDestroy'])->name('course-categories.destroy');

    // Course Content
    Route::get('/courses/{course}/content', [CourseContentController::class, 'adminIndex'])->name('course-contents.index');
    Route::get('/courses/{course}/content/create', [CourseContentController::class, 'adminCreate'])->name('course-contents.create');
    Route::post('/courses/{course}/content', [CourseContentController::class, 'adminStore'])->name('course-contents.store');
    Route::get('/courses/{course}/content/{content}', [CourseContentController::class, 'adminShow'])->name('course-contents.show');
    Route::get('/courses/{course}/content/{content}/edit', [CourseContentController::class, 'adminEdit'])->name('course-contents.edit');
    Route::put('/courses/{course}/content/{content}', [CourseContentController::class, 'adminUpdate'])->name('course-contents.update');
    Route::delete('/courses/{course}/content/{content}', [CourseContentController::class, 'adminDestroy'])->name('course-contents.destroy');

    // Course Quizzes
    Route::get('/courses/{course}/quiz', [CourseQuizController::class, 'adminIndex'])->name('course-quizzes.index');
    Route::get('/courses/{course}/quiz/create', [CourseQuizController::class, 'adminCreate'])->name('course-quizzes.create');
    Route::post('/courses/{course}/quiz', [CourseQuizController::class, 'adminStore'])->name('course-quizzes.store');
    Route::get('/courses/{course}/quiz/{quiz}', [CourseQuizController::class, 'adminShow'])->name('course-quizzes.show');
    Route::get('/courses/{course}/quiz/{quiz}/edit', [CourseQuizController::class, 'adminEdit'])->name('course-quizzes.edit');
    Route::put('/courses/{course}/quiz/{quiz}', [CourseQuizController::class, 'adminUpdate'])->name('course-quizzes.update');
    Route::delete('/courses/{course}/quiz/{quiz}', [CourseQuizController::class, 'adminDestroy'])->name('course-quizzes.destroy');

    // Quiz Questions
    Route::get('/courses/{course}/quiz/{quiz}/questions', [QuizQuestionController::class, 'index'])->name('quiz-questions.index');
    Route::post('/courses/{course}/quiz/{quiz}/questions', [QuizQuestionController::class, 'store'])->name('quiz-questions.store');
    Route::put('/courses/{course}/quiz/{quiz}/questions/{question}', [QuizQuestionController::class, 'update'])->name('quiz-questions.update');
    Route::delete('/courses/{course}/quiz/{quiz}/questions/{question}', [QuizQuestionController::class, 'destroy'])->name('quiz-questions.destroy');

    // Quiz Submissions & Grading
    Route::get('/courses/{course}/quiz/{quiz}/submissions', [QuizSubmissionController::class, 'submissions'])->name('course-quizzes.submissions');
    Route::get('/courses/{course}/quiz/{quiz}/submissions/{submission}', [QuizSubmissionController::class, 'viewSubmission'])->name('quiz.view-submission');
    Route::post('/courses/{course}/quiz/{quiz}/submissions/{submission}/review', [QuizSubmissionController::class, 'markReviewed'])->name('quiz.mark-reviewed');
    Route::post('/courses/{course}/quiz/{quiz}/submissions/{submission}/feedback', [QuizSubmissionController::class, 'saveFeedback'])->name('quiz.save-feedback');

    // Facilitators
    Route::get('/facilitators', [FacilitatorController::class, 'adminIndex'])->name('facilitators.index');
    Route::get('/facilitators/create', [FacilitatorController::class, 'adminCreate'])->name('facilitators.create');
    Route::post('/facilitators', [FacilitatorController::class, 'adminStore'])->name('facilitators.store');
    Route::get('/facilitators/{facilitator}', [FacilitatorController::class, 'adminShow'])->name('facilitators.show');
    Route::get('/facilitators/{facilitator}/edit', [FacilitatorController::class, 'adminEdit'])->name('facilitators.edit');
    Route::put('/facilitators/{facilitator}', [FacilitatorController::class, 'adminUpdate'])->name('facilitators.update');
    Route::delete('/facilitators/{facilitator}', [FacilitatorController::class, 'adminDestroy'])->name('facilitators.destroy');

    // Live Sessions
    Route::get('/courses/{course}/live-session', [LiveSessionController::class, 'adminIndex'])->name('live-sessions.index');
    Route::get('/courses/{course}/live-session/create', [LiveSessionController::class, 'adminCreate'])->name('live-sessions.create');
    Route::post('/courses/{course}/live-session', [LiveSessionController::class, 'adminStore'])->name('live-sessions.store');
    Route::get('/courses/{course}/live-session/{session}', [LiveSessionController::class, 'adminShow'])->name('live-sessions.show');
    Route::get('/courses/{course}/live-session/{session}/edit', [LiveSessionController::class, 'adminEdit'])->name('live-sessions.edit');
    Route::put('/courses/{course}/live-session/{session}', [LiveSessionController::class, 'adminUpdate'])->name('live-sessions.update');
    Route::delete('/courses/{course}/live-session/{session}', [LiveSessionController::class, 'adminDestroy'])->name('live-sessions.destroy');

    // Enrollments
    Route::get('/course-enrollments', [CourseEnrollmentController::class, 'adminIndex'])->name('course-enrollments.index');
    Route::get('/course-enrollments/{enrollment}', [CourseEnrollmentController::class, 'adminShow'])->name('course-enrollments.show');
    Route::put('/course-enrollments/{enrollment}', [CourseEnrollmentController::class, 'adminUpdate'])->name('course-enrollments.update');

    // Payments (Course Enrollment Payments)
    Route::get('/course-payments', [CoursePaymentController::class, 'adminIndex'])->name('course-payments.index');
    Route::get('/course-payments/{payment}', [CoursePaymentController::class, 'adminShow'])->name('course-payments.show');
    Route::post('/course-payments/{payment}/approve', [CoursePaymentController::class, 'approve'])->name('course-payments.approve');
    Route::post('/course-payments/{payment}/reject', [CoursePaymentController::class, 'reject'])->name('course-payments.reject');

    // Feedback & Contact Messages
    Route::get('/feedback', [ContactMessageController::class, 'adminIndex'])->name('feedback.index');
    Route::get('/feedback/{contact}', [ContactMessageController::class, 'adminShow'])->name('feedback.show');
    Route::post('/feedback/{contact}/respond', [ContactMessageController::class, 'storeResponse'])->name('feedback.respond');
    Route::delete('/feedback/{contact}', [ContactMessageController::class, 'destroy'])->name('feedback.destroy');

    // Discussion Moderation
    Route::get('/discussions', [CourseDiscussionController::class, 'adminIndex'])->name('discussions.index');
    Route::get('/discussions/{discussion}', [CourseDiscussionController::class, 'adminShow'])->name('discussions.show');
    Route::post('/discussions/{discussion}/pin', [CourseDiscussionController::class, 'togglePin'])->name('discussions.pin');
    Route::post('/discussions/{discussion}/lock', [CourseDiscussionController::class, 'toggleLock'])->name('discussions.lock');
    Route::delete('/discussions/{discussion}', [CourseDiscussionController::class, 'adminDestroy'])->name('discussions.destroy');

    // Homepage Settings
    Route::prefix('homepage-settings')->name('homepage-settings.')->group(function () {
        Route::get('/', [HomepageSettingController::class, 'index'])->name('index');
        Route::get('/section/{section}', [HomepageSettingController::class, 'editSection'])->name('edit-section');
        Route::put('/section/{section}/{key}', [HomepageSettingController::class, 'updateSetting'])->name('update-setting');
        Route::post('/section/{section}', [HomepageSettingController::class, 'updateSection'])->name('update-section');
        Route::delete('/section/{section}/{key}', [HomepageSettingController::class, 'destroy'])->name('destroy');
        Route::post('/initialize-defaults', [HomepageSettingController::class, 'initializeDefaults'])->name('initialize-defaults');
        
        // Course Display Settings
        Route::get('/course-display', [HomepageSettingController::class, 'showCourseDisplaySettings'])->name('course-display');
        Route::put('/course-display', [HomepageSettingController::class, 'updateCourseDisplaySettings'])->name('update-course-display');
    });

    // Site Builder
    Route::prefix('site-builder')->name('site-builder.')->group(function () {
        Route::get('/', [SiteBuilderController::class, 'index'])->name('index');
        
        // Logos
        Route::get('/logos', [SiteBuilderController::class, 'editLogos'])->name('logos');
        Route::post('/logos', [SiteBuilderController::class, 'updateLogos'])->name('update-logos');
        
        // Colors
        Route::get('/colors', [SiteBuilderController::class, 'editColors'])->name('colors');
        Route::post('/colors', [SiteBuilderController::class, 'updateColors'])->name('update-colors');
        
        // Typography
        Route::get('/typography', [SiteBuilderController::class, 'editTypography'])->name('typography');
        Route::post('/typography', [SiteBuilderController::class, 'updateTypography'])->name('update-typography');
        
        // Page Titles
        Route::get('/page-titles', [SiteBuilderController::class, 'editPageTitles'])->name('page-titles');
        Route::post('/page-titles', [SiteBuilderController::class, 'updatePageTitles'])->name('update-page-titles');
        
        // Design & Layout
        Route::get('/design', [SiteBuilderController::class, 'editDesign'])->name('design');
        Route::post('/design', [SiteBuilderController::class, 'updateDesign'])->name('update-design');
    });

    // Email Testing Routes
    Route::prefix('email-testing')->name('email-testing.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmailTestController::class, 'index'])->name('index');
        Route::post('/send-verification', [\App\Http\Controllers\Admin\EmailTestController::class, 'sendVerificationEmail'])->name('send-verification');
        Route::post('/send-contact', [\App\Http\Controllers\Admin\EmailTestController::class, 'sendContactResponseEmail'])->name('send-contact');
        Route::get('/config', [\App\Http\Controllers\Admin\EmailTestController::class, 'viewConfig'])->name('config');
    });
    
    // ========== SERVICES ==========
    Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
    Route::get('/services/{service}/requests', [\App\Http\Controllers\Admin\ServiceController::class, 'requests'])->name('services.requests');
    
    // ========== GALLERIES ==========
    Route::resource('galleries', \App\Http\Controllers\Admin\GalleryController::class);
    Route::delete('/galleries/{image}/image', [\App\Http\Controllers\Admin\GalleryController::class, 'deleteImage'])->name('galleries.delete-image');
    Route::post('/galleries/{gallery}/reorder-images', [\App\Http\Controllers\Admin\GalleryController::class, 'reorderImages'])->name('galleries.reorder-images');
    
    // ========== CAROUSEL MANAGEMENT ==========
    Route::get('/carousel', [\App\Http\Controllers\Admin\CarouselController::class, 'index'])->name('carousel.index');
    Route::post('/carousel/upload', [\App\Http\Controllers\Admin\CarouselController::class, 'upload'])->name('carousel.upload');
    Route::put('/carousel/{id}/update', [\App\Http\Controllers\Admin\CarouselController::class, 'update'])->name('carousel.update');
    Route::delete('/carousel/{id}/delete', [\App\Http\Controllers\Admin\CarouselController::class, 'destroy'])->name('carousel.destroy');
    Route::post('/carousel/reorder', [\App\Http\Controllers\Admin\CarouselController::class, 'reorder'])->name('carousel.reorder');
});

// ========================================
// TUTOR/INSTRUCTOR COURSE MANAGEMENT ROUTES
// ========================================
Route::middleware(['auth', 'verified', 'role:instructor'])->prefix('tutor')->name('tutor.')->group(function () {
    // Course Management
    Route::get('/courses', [CourseController::class, 'adminIndex'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'adminCreate'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'adminStore'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'adminShow'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'adminEdit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'adminUpdate'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'adminDestroy'])->name('courses.destroy');

    // Course Content
    Route::get('/courses/{course}/content', [CourseContentController::class, 'adminIndex'])->name('course-contents.index');
    Route::get('/courses/{course}/content/create', [CourseContentController::class, 'adminCreate'])->name('course-contents.create');
    Route::post('/courses/{course}/content', [CourseContentController::class, 'adminStore'])->name('course-contents.store');
    Route::get('/courses/{course}/content/{content}', [CourseContentController::class, 'adminShow'])->name('course-contents.show');
    Route::get('/courses/{course}/content/{content}/edit', [CourseContentController::class, 'adminEdit'])->name('course-contents.edit');
    Route::put('/courses/{course}/content/{content}', [CourseContentController::class, 'adminUpdate'])->name('course-contents.update');
    Route::delete('/courses/{course}/content/{content}', [CourseContentController::class, 'adminDestroy'])->name('course-contents.destroy');

    // Course Quizzes
    Route::get('/courses/{course}/quiz', [CourseQuizController::class, 'adminIndex'])->name('course-quizzes.index');
    Route::get('/courses/{course}/quiz/create', [CourseQuizController::class, 'adminCreate'])->name('course-quizzes.create');
    Route::post('/courses/{course}/quiz', [CourseQuizController::class, 'adminStore'])->name('course-quizzes.store');
    Route::get('/courses/{course}/quiz/{quiz}', [CourseQuizController::class, 'adminShow'])->name('course-quizzes.show');
    Route::get('/courses/{course}/quiz/{quiz}/edit', [CourseQuizController::class, 'adminEdit'])->name('course-quizzes.edit');
    Route::put('/courses/{course}/quiz/{quiz}', [CourseQuizController::class, 'adminUpdate'])->name('course-quizzes.update');
    Route::delete('/courses/{course}/quiz/{quiz}', [CourseQuizController::class, 'adminDestroy'])->name('course-quizzes.destroy');

    // Quiz Questions
    Route::get('/courses/{course}/quiz/{quiz}/questions', [QuizQuestionController::class, 'index'])->name('quiz-questions.index');
    Route::post('/courses/{course}/quiz/{quiz}/questions', [QuizQuestionController::class, 'store'])->name('quiz-questions.store');
    Route::put('/courses/{course}/quiz/{quiz}/questions/{question}', [QuizQuestionController::class, 'update'])->name('quiz-questions.update');
    Route::delete('/courses/{course}/quiz/{quiz}/questions/{question}', [QuizQuestionController::class, 'destroy'])->name('quiz-questions.destroy');

    // Quiz Submissions & Grading
    Route::get('/courses/{course}/quiz/{quiz}/submissions', [QuizSubmissionController::class, 'submissions'])->name('course-quizzes.submissions');
    Route::get('/courses/{course}/quiz/{quiz}/submissions/{submission}', [QuizSubmissionController::class, 'viewSubmission'])->name('quiz.view-submission');
    Route::post('/courses/{course}/quiz/{quiz}/submissions/{submission}/review', [QuizSubmissionController::class, 'markReviewed'])->name('quiz.mark-reviewed');
    Route::post('/courses/{course}/quiz/{quiz}/submissions/{submission}/feedback', [QuizSubmissionController::class, 'saveFeedback'])->name('quiz.save-feedback');

    // Live Sessions
    Route::get('/courses/{course}/live-session', [LiveSessionController::class, 'adminIndex'])->name('live-sessions.index');
    Route::get('/courses/{course}/live-session/create', [LiveSessionController::class, 'adminCreate'])->name('live-sessions.create');
    Route::post('/courses/{course}/live-session', [LiveSessionController::class, 'adminStore'])->name('live-sessions.store');
    Route::get('/courses/{course}/live-session/{session}', [LiveSessionController::class, 'adminShow'])->name('live-sessions.show');
    Route::get('/courses/{course}/live-session/{session}/edit', [LiveSessionController::class, 'adminEdit'])->name('live-sessions.edit');
    Route::put('/courses/{course}/live-session/{session}', [LiveSessionController::class, 'adminUpdate'])->name('live-sessions.update');
    Route::delete('/courses/{course}/live-session/{session}', [LiveSessionController::class, 'adminDestroy'])->name('live-sessions.destroy');

    // Enrollments
    Route::get('/courses/{course}/enrollments', [CourseEnrollmentController::class, 'adminIndex'])->name('course-enrollments.index');
    Route::get('/course-enrollments/{enrollment}', [CourseEnrollmentController::class, 'adminShow'])->name('course-enrollments.show');
    Route::put('/course-enrollments/{enrollment}', [CourseEnrollmentController::class, 'adminUpdate'])->name('course-enrollments.update');

    // Discussions
    Route::get('/courses/{course}/discussions', [CourseDiscussionController::class, 'adminIndex'])->name('discussions.index');
    Route::get('/discussions/{discussion}', [CourseDiscussionController::class, 'adminShow'])->name('discussions.show');
    Route::post('/discussions/{discussion}/pin', [CourseDiscussionController::class, 'togglePin'])->name('discussions.pin');
    Route::post('/discussions/{discussion}/lock', [CourseDiscussionController::class, 'toggleLock'])->name('discussions.lock');
    Route::delete('/discussions/{discussion}', [CourseDiscussionController::class, 'adminDestroy'])->name('discussions.destroy');

    // Announcements
    Route::get('/courses/{course}/announcement/create', [CourseBulkMessageController::class, 'create'])->name('course.announcement.create');
    Route::post('/courses/{course}/announcement', [CourseBulkMessageController::class, 'store'])->name('course.announcement.store');
    Route::get('/courses/{course}/announcement/history', [CourseBulkMessageController::class, 'history'])->name('course.announcement.history');
    Route::get('/announcement/{message}', [CourseBulkMessageController::class, 'show'])->name('course.announcement.show');
    Route::post('/announcement/{message}/send', [CourseBulkMessageController::class, 'send'])->name('course.announcement.send');
});


<?php

$output = shell_exec('cd c:\\Users\\Ogochukwu\\Desktop\\PROJECTS\\PHP\\online_lms && php artisan tinker 2>&1 <<\'EOF\'
use App\Models\Course;
use App\Models\User;
use App\Models\CourseEnrollee;
use App\Models\CoursePayment;

// Get a user
$user = User::first();
if (!$user) {
    echo "No users found\n";
    exit;
}

// Get a course
$course = Course::where("is_free", false)->first() ?? Course::first();
if (!$course) {
    echo "No courses found\n";
    exit;
}

echo "User: " . $user->id . " - " . $user->email . "\n";
echo "Course: " . $course->id . " - " . $course->title . "\n";
echo "Course Fee: " . $course->fee . "\n";
echo "Is Free: " . ($course->is_free ? "Yes" : "No") . "\n";

// Simulate creating an enrollment
$enrollment = CourseEnrollee::create([
    'user_id' => $user->id,
    'course_id' => $course->id,
    'course_date_id' => null,
    'course_venue_id' => null,
    'status' => 'pending',
    'payment_status' => 'pending',
    'enrolled_at' => now(),
]);

echo "Enrollment ID: " . $enrollment->id . "\n";

// Create payment
$payment = CoursePayment::create([
    'course_enrollee_id' => $enrollment->id,
    'user_id' => $user->id,
    'course_id' => $course->id,
    'amount' => $course->fee ?? 0,
    'currency' => $course->currency,
    'reference_id' => 'TEST-' . time() . '-' . $user->id,
    'payment_method' => $course->is_free ? 'free' : 'pending',
    'status' => $course->is_free ? 'completed' : 'pending',
]);

echo "Payment ID: " . $payment->id . "\n";
echo "Payment Amount: " . $payment->amount . "\n";
echo "Payment Status: " . $payment->status . "\n";

// Check if route can be generated
$routeUrl = route('course.payment.show', ['payment' => $payment->id]);
echo "Route URL: " . $routeUrl . "\n";

// Try to fetch the payment back
$paymentCheck = CoursePayment::find($payment->id);
if ($paymentCheck) {
    echo "Payment verified: " . $paymentCheck->id . "\n";
    $paymentCheck->load('enrollment');
    echo "Enrollment loaded: " . ($paymentCheck->enrollment ? "Yes" : "No") . "\n";
} else {
    echo "Payment NOT found!\n";
}

EOF
');

echo $output;

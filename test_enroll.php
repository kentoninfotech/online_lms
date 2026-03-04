#!/usr/bin/env php
<?php

require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';

use App\Models\Course;
use App\Models\User;
use App\Models\CourseEnrollee;
use App\Models\CoursePayment;
use Illuminate\Support\Facades\Auth;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test data exists
$user = User::first();
$course = Course::where('is_free', false)->first() ?? Course::first();

if (!$user || !$course) {
    echo "Missing user or course\n";
    exit(1);
}

echo "=== Test Enrollment Flow ===\n";
echo "User: {$user->id} - {$user->email}\n";
echo "Course: {$course->id} - {$course->title} (Fee: {$course->fee})\n";

// Try to create enrollment
try {
    $enrollment = CourseEnrollee::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'course_date_id' => null,
        'course_venue_id' => null,
        'status' => 'pending',
        'payment_status' => 'pending',
        'enrolled_at' => now(),
    ]);
    echo "Enrollment created: ID {$enrollment->id}\n";
    
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
    echo "Payment created: ID {$payment->id} (Amount: {$payment->amount})\n";
    
    // Test route generation
    $url = route('course.payment.show', ['payment' => $payment->id]);
    echo "Route URL: {$url}\n";
    
    // Verify payment can be loaded
    $loaded = CoursePayment::with('enrollment', 'course')->find($payment->id);
    if ($loaded) {
        echo "Payment loaded successfully\n";
        echo "Enrollment: " . ($loaded->enrollment ? 'OK' : 'MISSING') . "\n";
        echo "Course: " . ($loaded->course ? 'OK' : 'MISSING') . "\n";
    } else {
        echo "Failed to load payment!\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

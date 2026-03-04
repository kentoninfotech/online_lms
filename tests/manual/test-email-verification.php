#!/usr/bin/env php
<?php

/**
 * Email Verification Flow Test Script
 * 
 * This script tests the complete email verification flow:
 * 1. User Registration
 * 2. Email Sending (with error handling)
 * 3. Email Verification
 * 4. Login After Verification
 * 5. Resend Email
 * 
 * Run: php artisan tinker < tests/manual/test-email-verification.php
 * Or:  php tests/manual/test-email-verification.php
 */

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║     Email Verification Flow Test Script                   ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

// Import required classes
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

// =================================================================
// TEST 1: CREATE TEST USERS
// =================================================================
echo "📝 TEST 1: Creating Test Users\n";
echo "─────────────────────────────────────────────────────────────\n";

// Clean up old test users
User::where('email', 'like', 'test-verif%@example.com')->forceDelete();

$testUsers = [
    ['name' => 'Test Student', 'email' => 'test-verif-student@example.com', 'type' => 'student'],
    ['name' => 'Test Instructor', 'email' => 'test-verif-instructor@example.com', 'type' => 'instructor'],
    ['name' => 'Test Parent', 'email' => 'test-verif-parent@example.com', 'type' => 'parent'],
];

foreach ($testUsers as $userData) {
    $user = User::create([
        'name' => $userData['name'],
        'email' => $userData['email'],
        'password' => Hash::make('TestPassword123!!'),
        'user_type' => $userData['type'],
    ]);

    echo "✅ Created: {$user->name} ({$user->user_type}) - {$user->email}\n";
}

echo "\n";

// =================================================================
// TEST 2: VERIFY EMAIL SENDING
// =================================================================
echo "📧 TEST 2: Testing Email Verification Notification\n";
echo "─────────────────────────────────────────────────────────────\n";

$testUser = User::where('email', 'test-verif-student@example.com')->first();

try {
    echo "Sending verification email to: {$testUser->email}\n";
    $testUser->sendEmailVerificationNotification();
    echo "✅ Email notification sent successfully\n";
    echo "📍 Check mail logs at: storage/logs/laravel.log\n";
} catch (\Exception $e) {
    echo "❌ Error sending email: {$e->getMessage()}\n";
}

echo "\n";

// =================================================================
// TEST 3: VERIFY EMAIL STATUS
// =================================================================
echo "🔍 TEST 3: Checking Email Verification Status\n";
echo "─────────────────────────────────────────────────────────────\n";

foreach ($testUsers as $userData) {
    $user = User::where('email', $userData['email'])->first();
    $verified = $user->hasVerifiedEmail() ? '✅ VERIFIED' : '❌ NOT VERIFIED';
    echo "{$user->name}: {$verified}\n";
}

echo "\n";

// =================================================================
// TEST 4: MARK EMAIL AS VERIFIED
// =================================================================
echo "✨ TEST 4: Marking Email as Verified\n";
echo "─────────────────────────────────────────────────────────────\n";

$testUser = User::where('email', 'test-verif-student@example.com')->first();
$testUser->markEmailAsVerified();

echo "✅ Marked {$testUser->email} as verified\n";
echo "Verified at: {$testUser->email_verified_at}\n";

echo "\n";

// =================================================================
// TEST 5: LOGIN TEST
// =================================================================
echo "🔐 TEST 5: Testing Login Endpoints\n";
echo "─────────────────────────────────────────────────────────────\n";

// Verify one more user for login test
$loginTestUser = User::where('email', 'test-verif-instructor@example.com')->first();
$loginTestUser->markEmailAsVerified();

echo "✅ Marked {$loginTestUser->email} as verified for login test\n\n";

echo "To test API endpoints, use CURL commands:\n\n";

echo "1️⃣  Register new user:\n";
echo "   curl -X POST http://localhost:8000/api/auth/register \\\n";
echo "     -H 'Content-Type: application/json' \\\n";
echo "     -d '{\n";
echo "       \"name\": \"New User\",\n";
echo "       \"email\": \"newuser@example.com\",\n";
echo "       \"password\": \"SecurePassword123\",\n";
echo "       \"password_confirmation\": \"SecurePassword123\",\n";
echo "       \"user_type\": \"student\"\n";
echo "     }'\n\n";

echo "2️⃣  Login (unverified email - should fail):\n";
echo "   curl -X POST http://localhost:8000/api/auth/login \\\n";
echo "     -H 'Content-Type: application/json' \\\n";
echo "     -d '{\"email\": \"test-verif-parent@example.com\", \"password\": \"TestPassword123!!\"}'\n\n";

echo "3️⃣  Login (verified email - should succeed):\n";
echo "   curl -X POST http://localhost:8000/api/auth/login \\\n";
echo "     -H 'Content-Type: application/json' \\\n";
echo "     -d '{\"email\": \"test-verif-instructor@example.com\", \"password\": \"TestPassword123!!\"}'\n\n";

echo "4️⃣  Resend verification email:\n";
echo "   curl -X POST http://localhost:8000/api/auth/resend-verification \\\n";
echo "     -H 'Content-Type: application/json' \\\n";
echo "     -d '{\"email\": \"test-verif-parent@example.com\"}'\n\n";

echo "5️⃣  Verify email with token:\n";
echo "   curl -X POST http://localhost:8000/api/auth/verify-email \\\n";
echo "     -H 'Content-Type: application/json' \\\n";
echo "     -d '{\"id\": 1, \"hash\": \"<hash>\"}'\n";

echo "\n";

// =================================================================
// TEST SUMMARY
// =================================================================
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║ Test Summary                                                  ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n";

$allUsers = User::where('email', 'like', 'test-verif%@example.com')->get();

echo "\nTest Users Created: " . $allUsers->count() . "\n\n";

foreach ($allUsers as $user) {
    $status = $user->hasVerifiedEmail() ? '✅ VERIFIED' : '⏳ PENDING';
    echo "  • {$user->email} - {$status}\n";
}

echo "\n📝 Check email logs:\n";
echo "   tail -f storage/logs/laravel.log | grep -i 'email\\|mail'\n\n";

echo "🧪 Run Feature Tests:\n";
echo "   php artisan test tests/Feature/API/EmailVerificationTest.php\n\n";

echo "✅ Email verification flow setup is complete!\n\n";

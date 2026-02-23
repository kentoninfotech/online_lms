#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Contracts\Http\Kernel;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;

$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application to initialize all services
$kernel = $app->make(Kernel::class);
$request = \Illuminate\Http\Request::capture();

// Create/find test user
$testUser = User::where('email', 'test@example.com')->first();

if (!$testUser) {
    $testUser = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('test123456'),
        'user_type' => 'student',
    ]);
    echo "✅ Created new test user\n";
} else {
    echo "✅ Found existing test user\n";
}

// Reset email verification
$testUser->update(['email_verified_at' => null]);

echo "\n═══════════════════════════════════════════════════════════\n";
echo "EMAIL VERIFICATION TEST\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "📧 Test User: {$testUser->email}\n";
echo "✋ Email Status: " . ($testUser->hasVerifiedEmail() ? '✅ Verified' : '❌ Unverified') . "\n";
echo "🔑 Password: test123456\n\n";

echo "Sending verification email...\n\n";

try {
    // Send the verification email
    $testUser->notify(new VerifyEmail());
    
    echo "✅ SUCCESS: Verification email sent!\n\n";
    echo "📨 Email Configuration:\n";
    echo "   • Mailer: " . config('mail.mailer') . "\n";
    echo "   • Host: " . config('mail.host') . "\n";
    echo "   • Port: " . config('mail.port') . "\n";
    echo "   • Encryption: " . config('mail.encryption') . "\n";
    echo "   • From: " . config('mail.from.address') . "\n";
    echo "   • Username: " . config('mail.username') . "\n\n";
    
    echo "📝 Instructions:\n";
    echo "   1. Log in with the test user:\n";
    echo "      Email: test@example.com\n";
    echo "      Password: test123456\n\n";
    echo "   2. You will be redirected to the verification page\n";
    echo "   3. Check the email inbox at test@example.com\n";
    echo "   4. Click the verification link in the email\n";
    echo "   5. Return to the app and log in again\n\n";
    
    echo "═══════════════════════════════════════════════════════════\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "   ✓ Check MAIL_MAILER is set to 'smtp' (not 'log')\n";
    echo "   ✓ Verify MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD\n";
    echo "   ✓ Check MAIL_FROM_ADDRESS is valid\n";
    echo "   ✓ Ensure MAIL_ENCRYPTION matches (ssl for port 465)\n";
    echo "   ✓ Check firewall allows outbound SMTP\n";
    echo "   ✓ Test SMTP credentials in your email client\n\n";
    echo "Full Error: " . $e->getMessage() . "\n\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

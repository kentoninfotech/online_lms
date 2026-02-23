<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get a test user or create one
$user = \App\Models\User::where('email', 'test@example.com')->first();

if (!$user) {
    $user = \App\Models\User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'user_type' => 'student',
        'password' => bcrypt('password123'),
        'email_verified_at' => null,
    ]);
    echo "Created test user: {$user->email}\n";
} else {
    // Reset email_verified_at to simulate unverified user
    $user->update(['email_verified_at' => null]);
    echo "Using existing user: {$user->email}\n";
}

// Send the verification email
echo "\nSending verification email...\n";
$user->sendEmailVerificationNotification();

echo "✓ Verification email notification sent!\n";
echo "Check your email configured in .env (MAIL_TO_ADDRESS)\n";
echo "Email address: {$user->email}\n";

<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Contracts\Http\Kernel;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Mail;

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
)->send();

// Find or create a test user
$testUser = User::firstOrCreate(
    ['email' => 'test@example.com'],
    [
        'name' => 'Test User',
        'password' => bcrypt('test123456'),
    ]
);

// Mark email as unverified for testing
$testUser->update(['email_verified_at' => null]);

echo "═══════════════════════════════════════════════════════════\n";
echo "EMAIL VERIFICATION TEST\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "📧 Test User: {$testUser->email}\n";
echo "✋ Email Status: " . ($testUser->hasVerifiedEmail() ? '✅ Verified' : '❌ Unverified') . "\n\n";

echo "Sending verification email...\n\n";

try {
    // Send the verification email
    $testUser->notify(new VerifyEmail());
    
    echo "✅ SUCCESS: Verification email sent!\n\n";
    echo "📨 Email Details:\n";
    echo "   • Recipient: {$testUser->email}\n";
    echo "   • From: " . config('mail.from.address') . "\n";
    echo "   • Host: " . config('mail.host') . "\n";
    echo "   • Port: " . config('mail.port') . "\n";
    echo "   • Encryption: " . config('mail.encryption') . "\n";
    echo "   • Username: " . config('mail.username') . "\n\n";
    
    echo "📝 Next Steps:\n";
    echo "   1. Check the email at {$testUser->email}\n";
    echo "   2. Look for a verification link\n";
    echo "   3. Click the link to verify the email\n";
    echo "   4. Return to login\n\n";
    
    echo "═══════════════════════════════════════════════════════════\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "   • Check MAIL_MAILER is set to 'smtp'\n";
    echo "   • Verify MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD\n";
    echo "   • Check MAIL_FROM_ADDRESS is valid\n";
    echo "   • Ensure MAIL_ENCRYPTION matches the server settings\n";
    echo "   • Check firewall allows outbound SMTP (port 465/587)\n\n";
    echo "Error Details: " . $e->getMessage() . "\n";
}

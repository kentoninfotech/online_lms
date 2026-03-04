#!/usr/bin/env php
<?php

/**
 * Test Script: Verify Remember Me (30 Day Session) Configuration
 * 
 * Run this from the project root: php test-remember-me.php
 */

echo "\n";
echo "========================================\n";
echo "  Remember Me Feature - Verification Test\n";
echo "========================================\n\n";

// Load Laravel
try {
    require __DIR__ . '/bootstrap/app.php';
    $app = require __DIR__ . '/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
} catch (\Exception $e) {
    echo "❌ Error: Could not load Laravel application\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

$checks = [];

// Check 1: Session Lifetime Configuration
echo "1️⃣  Checking Session Lifetime Configuration\n";
echo "   ➜ Expected: 43200 minutes (30 days)\n";
$sessionLifetime = config('session.lifetime');
$expectedLifetime = 43200;
if ($sessionLifetime == $expectedLifetime) {
    echo "   ✅ Session lifetime: $sessionLifetime minutes (30 days)\n";
    $checks['session_lifetime'] = true;
} else {
    echo "   ❌ Session lifetime: $sessionLifetime minutes (Expected: $expectedLifetime)\n";
    echo "   💡 Update SESSION_LIFETIME=43200 in .env file\n";
    $checks['session_lifetime'] = false;
}
echo "\n";

// Check 2: Session Expire On Close
echo "2️⃣  Checking Session Expire On Close\n";
echo "   ➜ Expected: false (keep session after browser closes)\n";
$expireOnClose = config('session.expire_on_close');
if ($expireOnClose === false) {
    echo "   ✅ Session expire on close: disabled\n";
    $checks['expire_on_close'] = true;
} else {
    echo "   ❌ Session expire on close: enabled\n";
    echo "   💡 Update SESSION_EXPIRE_ON_CLOSE=false in .env file\n";
    $checks['expire_on_close'] = false;
}
echo "\n";

// Check 3: Session Driver
echo "3️⃣  Checking Session Driver\n";
echo "   ➜ Expected: database\n";
$driver = config('session.driver');
if ($driver === 'database') {
    echo "   ✅ Session driver: $driver\n";
    $checks['session_driver'] = true;
} else {
    echo "   ❌ Session driver: $driver (Expected: database)\n";
    echo "   💡 Update SESSION_DRIVER=database in .env file\n";
    $checks['session_driver'] = false;
}
echo "\n";

// Check 4: Database Sessions Table
echo "4️⃣  Checking Database Sessions Table\n";
echo "   ➜ Expected: sessions table exists\n";
try {
    $schema = \Illuminate\Support\Facades\Schema::connection(config('session.connection'));
    if ($schema->hasTable(config('session.table'))) {
        echo "   ✅ Sessions table exists\n";
        $checks['sessions_table'] = true;
    } else {
        echo "   ❌ Sessions table not found\n";
        echo "   💡 Run: php artisan migrate\n";
        $checks['sessions_table'] = false;
    }
} catch (\Exception $e) {
    echo "   ❌ Error checking sessions table: " . $e->getMessage() . "\n";
    $checks['sessions_table'] = false;
}
echo "\n";

// Check 5: User Model Remember Token
echo "5️⃣  Checking User Model\n";
echo "   ➜ Expected: remember_token in hidden attributes\n";
try {
    $user = new \App\Models\User();
    $hidden = $user->getHidden();
    if (in_array('remember_token', $hidden)) {
        echo "   ✅ remember_token is hidden from serialization\n";
        $checks['remember_token_hidden'] = true;
    } else {
        echo "   ⚠️  remember_token might not be properly hidden\n";
        echo "   💡 Ensure 'remember_token' is in User model's \$hidden array\n";
        $checks['remember_token_hidden'] = false;
    }
} catch (\Exception $e) {
    echo "   ❌ Error checking User model: " . $e->getMessage() . "\n";
    $checks['remember_token_hidden'] = false;
}
echo "\n";

// Check 6: Users Table Remember Token Column
echo "6️⃣  Checking Users Table\n";
echo "   ➜ Expected: remember_token column exists\n";
try {
    $schema = \Illuminate\Support\Facades\Schema::connection(config('database.default'));
    if ($schema->hasColumn('users', 'remember_token')) {
        echo "   ✅ remember_token column exists in users table\n";
        $checks['remember_token_column'] = true;
    } else {
        echo "   ❌ remember_token column not found in users table\n";
        echo "   💡 Run: php artisan migrate\n";
        $checks['remember_token_column'] = false;
    }
} catch (\Exception $e) {
    echo "   ❌ Error checking users table: " . $e->getMessage() . "\n";
    $checks['remember_token_column'] = false;
}
echo "\n";

// Check 7: Session Cookie Configuration
echo "7️⃣  Checking Session Cookie Configuration\n";
echo "   ➜ Cookie Name: " . config('session.cookie') . "\n";
echo "   ➜ Secure: " . (config('session.secure') ? 'Yes' : 'No') . "\n";
echo "   ➜ HttpOnly: " . (config('session.http_only') ? 'Yes' : 'No') . "\n";
echo "   ➜ SameSite: " . config('session.same_site') . "\n";

$cookieConfig = true;
if (!config('session.http_only')) {
    echo "   ⚠️  HttpOnly is disabled (should be enabled for security)\n";
    $cookieConfig = false;
}
if (config('session.http_only')) {
    echo "   ✅ Session cookies are HttpOnly (JavaScript cannot access)\n";
}
$checks['cookie_config'] = $cookieConfig;
echo "\n";

// Check 8: Authentication Guard
echo "8️⃣  Checking Authentication Guard\n";
echo "   ➜ Expected: session driver\n";
try {
    $guard = config('auth.guards.web');
    if ($guard['driver'] === 'session' && $guard['provider'] === 'users') {
        echo "   ✅ Web guard configured correctly\n";
        echo "      Driver: " . $guard['driver'] . "\n";
        echo "      Provider: " . $guard['provider'] . "\n";
        $checks['auth_guard'] = true;
    } else {
        echo "   ❌ Web guard configuration issue\n";
        echo "   💡 Check config/auth.php\n";
        $checks['auth_guard'] = false;
    }
} catch (\Exception $e) {
    echo "   ❌ Error checking auth guard: " . $e->getMessage() . "\n";
    $checks['auth_guard'] = false;
}
echo "\n";

// Summary
echo "========================================\n";
echo "  TEST SUMMARY\n";
echo "========================================\n\n";

$passed = array_sum(array_filter($checks));
$total = count($checks);

if ($passed === $total) {
    echo "✅ All checks passed! Remember Me is properly configured.\n\n";
    echo "Keep users logged in for 30 days when they check 'Remember me'.\n";
} else {
    echo "⚠️  Some checks failed. Please review the issues above.\n\n";
    echo "Failed checks:\n";
    foreach ($checks as $check => $result) {
        if (!$result) {
            echo "   ❌ " . str_replace('_', ' ', ucfirst($check)) . "\n";
        }
    }
}

echo "\nPassed: $passed/$total checks\n";
echo "========================================\n\n";

exit($passed === $total ? 0 : 1);

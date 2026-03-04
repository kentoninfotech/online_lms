<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check instructor users
echo "=== INSTRUCTOR USERS ===\n";
$instructors = \App\Models\User::where('user_type', 'instructor')->get();
if ($instructors->count() > 0) {
    foreach ($instructors as $user) {
        echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
        echo "  Verified: " . ($user->email_verified_at ? "YES" : "NO") . "\n";
        echo "  Roles: " . ($user->roles->count() > 0 ? $user->roles->pluck('name')->join(', ') : "NONE") . "\n";
        if ($user->instructor) {
            echo "  Instructor Record: YES (ID: {$user->instructor->id})\n";
        } else {
            echo "  Instructor Record: NO\n";
        }
    }
} else {
    echo "No instructor users found\n";
}

// Check roles
echo "\n=== INSTRUCTOR ROLE ===\n";
$roles = \Spatie\Permission\Models\Role::where('name', 'instructor')->first();
if ($roles) {
    echo "Instructor role exists: YES\n";
} else {
    echo "Instructor role exists: NO\n";
}

// Check if routes are properly registered
echo "\n=== ROUTE STATUS ===\n";
$route = app('router')->getRoutes()->getByName('instructor.my-courses');
if ($route) {
    echo "Route 'instructor.my-courses' registered: YES\n";
    echo "  Path: " . $route->uri() . "\n";
    echo "  Methods: " . implode(', ', $route->methods()) . "\n";
} else {
    echo "Route 'instructor.my-courses' registered: NO\n";
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== File Upload & Homepage Settings Fix Verification ===\n\n";

// 1. Check directory structure
echo "1. Checking upload directories:\n";
$uploadsDir = public_path('uploads');
$dirs = ['branding', 'courses', 'facilitators', 'profiles'];
foreach ($dirs as $dir) {
    $path = $uploadsDir . '/' . $dir;
    $exists = is_dir($path);
    $writable = $exists && is_writable($path);
    echo "   - uploads/{$dir}: " . ($exists ? "EXISTS" : "MISSING") . " | WRITABLE: " . ($writable ? "YES" : "NO") . "\n";
}

// 2. Check database image paths
echo "\n2. Homepage Settings Image Paths in Database:\n";
$settings = \App\Models\HomepageSetting::whereNotNull('image_path')->limit(5)->get();
foreach ($settings as $s) {
    echo "   - {$s->section}/{$s->key}: {$s->image_path}\n";
}

// 3. Verify file existence
echo "\n3. Verifying files exist on disk:\n";
foreach ($settings as $s) {
    if ($s->image_path) {
        $filePath = public_path($s->image_path);
        $exists = file_exists($filePath);
        echo "   - {$s->image_path}: " . ($exists ? "EXISTS" : "MISSING") . "\n";
    }
}

// 4. Check homepage settings structure
echo "\n4. Homepage Settings Structure (getAllSections output):\n";
$homeSettings = \App\Models\HomepageSetting::getAllSections();
foreach ($homeSettings as $section => $settings) {
    echo "   [{$section}] => " . count($settings) . " items\n";
    if ($section === 'hero') {
        foreach ($settings as $key => $setting) {
            echo "       - {$key}: {$setting->value}\n";
        }
    }
}

// 5. Check asset paths
echo "\n5. Asset URL generation test:\n";
$testPath = 'uploads/branding/test.jpg';
$assetUrl = asset($testPath);
echo "   - asset('{$testPath}') => {$assetUrl}\n";

echo "\n=== Verification Complete ===\n";

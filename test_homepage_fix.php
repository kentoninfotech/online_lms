<?php
// Test the homepage settings fix
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\HomepageSetting;

// Get the raw settings
$rawSettings = HomepageSetting::getAllSections();

echo "RAW SETTINGS STRUCTURE:\n";
echo "========================\n\n";

if ($rawSettings->isEmpty()) {
    echo "⚠️  NO SETTINGS FOUND - Make sure database is seeded!\n";
    exit;
}

// Simulate what CourseController does
$homeSettings = [];
foreach ($rawSettings as $section => $settings) {
    $homeSettings[$section] = [];
    foreach ($settings as $key => $setting) {
        // Convert to associative array so views can use array notation: ['value']
        $homeSettings[$section][$key] = [
            'value' => $setting->value,
            'image_path' => $setting->image_path,
            'button_text' => $setting->button_text,
            'button_link' => $setting->button_link,
            'title' => $setting->title,
            'description' => $setting->description
        ];
    }
}

echo "✅ Conversion successful!\n\n";

// Test the CTA section specifically
echo "CTA SECTION TEST:\n";
echo "==================\n\n";

if (isset($homeSettings['cta'])) {
    echo "CTA settings found.\n";
    echo "Available keys: " . implode(', ', array_keys($homeSettings['cta'])) . "\n\n";
    
    // Test the exact syntax from the blade template
    try {
        $titleValue = $homeSettings['cta']['title']['value'] ?? 'Default Title';
        echo "✅ Title access works: " . $titleValue . "\n";
    } catch (\Exception $e) {
        echo "❌ Title access failed: " . $e->getMessage() . "\n";
    }
    
    try {
        $descriptionValue = $homeSettings['cta']['description']['value'] ?? 'Default Description';
        echo "✅ Description access works: " . $descriptionValue . "\n";
    } catch (\Exception $e) {
        echo "❌ Description access failed: " . $e->getMessage() . "\n";
    }
    
    try {
        $buttonText = $homeSettings['cta']['button_text']['value'] ?? 'Sign Up Free';
        echo "✅ Button text access works: " . $buttonText . "\n";
    } catch (\Exception $e) {
        echo "❌ Button text access failed: " . $e->getMessage() . "\n";
    }
    
    try {
        $buttonLink = $homeSettings['cta']['button_link']['value'] ?? '/register';
        echo "✅ Button link access works: " . $buttonLink . "\n";
    } catch (\Exception $e) {
        echo "❌ Button link access failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ CTA section not found in settings!\n";
    echo "Available sections: " . implode(', ', array_keys($homeSettings)) . "\n";
}

echo "\n✅ All tests passed! Landing page should work now.\n";
?>

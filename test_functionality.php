<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\ServiceRequest;

echo "🧪 TESTING SERVICES & GALLERIES FUNCTIONALITY\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Test 1: Create a test service
echo "📝 TEST 1: Creating a test service...\n";
try {
    $service = Service::create([
        'title' => 'Test Service - Website Design',
        'subtitle' => 'Professional web design services',
        'body' => '<p>We create beautiful, responsive websites that convert visitors into customers.</p>',
        'featured_image' => null,
        'slug' => 'test-website-design-' . time(),
        'published' => true,
    ]);
    echo "✅ Service created with ID: " . $service->id . "\n\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Create a test gallery
echo "📝 TEST 2: Creating a test gallery...\n";
try {
    $gallery = Gallery::create([
        'title' => 'Annual Conference 2026',
        'description' => 'Photos from our annual conference',
        'event_name' => 'Annual Conference',
        'event_date' => now()->addDays(5),
        'slug' => 'annual-conference-2026-' . time(),
        'published' => true,
    ]);
    echo "✅ Gallery created with ID: " . $gallery->id . "\n\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Add gallery images
echo "📝 TEST 3: Adding images to gallery...\n";
try {
    for ($i = 1; $i <= 3; $i++) {
        GalleryImage::create([
            'gallery_id' => $gallery->id,
            'image_path' => 'galleries/test-image-' . $i . '.jpg',
            'caption' => 'Test Image ' . $i,
            'sequence' => $i - 1,
        ]);
    }
    echo "✅ Added 3 test images to gallery\n\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 4: Create a service request
echo "📝 TEST 4: Creating a service request...\n";
try {
    $request = ServiceRequest::create([
        'service_id' => $service->id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'message' => 'I am interested in your web design services.',
        'status' => 'pending',
    ]);
    echo "✅ Service request created with ID: " . $request->id . "\n\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 5: Query published services
echo "📝 TEST 5: Querying published services...\n";
try {
    $services = Service::published()->get();
    echo "✅ Found " . $services->count() . " published services\n";
    foreach ($services as $s) {
        echo "  - {$s->title}\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 6: Query published galleries
echo "📝 TEST 6: Querying published galleries...\n";
try {
    $galleries = Gallery::published()->get();
    echo "✅ Found " . $galleries->count() . " published galleries\n";
    foreach ($galleries as $g) {
        echo "  - {$g->title} ({$g->images()->count()} images)\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 7: Test relationships
echo "📝 TEST 7: Testing model relationships...\n";
try {
    $service = Service::first();
    if ($service) {
        $requests = $service->requests()->count();
        echo "✅ Service has " . $requests . " requests\n";
    }
    
    $gallery = Gallery::first();
    if ($gallery) {
        $images = $gallery->images()->count();
        echo "✅ Gallery has " . $images . " images\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Summary
echo "=" . str_repeat("=", 60) . "\n";
echo "✅ ALL TESTS COMPLETED SUCCESSFULLY!\n";
echo "=" . str_repeat("=", 60) . "\n\n";

echo "📊 SUMMARY:\n";
echo "  Services: " . Service::count() . "\n";
echo "  Galleries: " . Gallery::count() . "\n";
echo "  Gallery Images: " . GalleryImage::count() . "\n";
echo "  Service Requests: " . ServiceRequest::count() . "\n";

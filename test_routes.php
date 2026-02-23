<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

$routes = Route::getRoutes()->getRoutes();

$serviceRoutes = [];
$galleryRoutes = [];
$carouselRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri;
    if (strpos($uri, 'service') !== false) {
        $serviceRoutes[] = $uri;
    }
    if (strpos($uri, 'gallery') !== false) {
        $galleryRoutes[] = $uri;
    }
    if (strpos($uri, 'carousel') !== false) {
        $carouselRoutes[] = $uri;
    }
}

echo "✅ SERVICE ROUTES:\n";
foreach ($serviceRoutes as $route) {
    echo "  - $route\n";
}

echo "\n✅ GALLERY ROUTES:\n";
foreach ($galleryRoutes as $route) {
    echo "  - $route\n";
}

echo "\n✅ CAROUSEL ROUTES:\n";
foreach ($carouselRoutes as $route) {
    echo "  - $route\n";
}

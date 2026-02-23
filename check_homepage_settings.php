<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$homeSettings = \App\Models\HomepageSetting::getAllSections();

echo "=== Homepage Settings Structure ===\n";
echo json_encode($homeSettings->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
echo "\n\n";

echo "=== Hero Section Details ===\n";
if (isset($homeSettings['hero'])) {
    foreach($homeSettings['hero'] as $key => $setting) {
        echo "{$key}: value='{$setting->value}', image_path='{$setting->image_path}'\n";
    }
}

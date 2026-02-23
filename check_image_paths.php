<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$settings = \App\Models\HomepageSetting::whereNotNull('image_path')->limit(10)->get();
echo "Image paths in database:\n";
foreach($settings as $s) {
    echo "{$s->section} - {$s->key}: {$s->image_path}\n";
}

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = [
    ['name' => 'Ultra HD 4K Gaming Monitor 27 inch', 'price' => 34999],
    ['name' => 'Pro Wireless Mechanical Keyboard RGB', 'price' => 8499],
    ['name' => 'Precision Ergonomic Gaming Mouse', 'price' => 3299],
    ['name' => 'Active Noise Cancelling Wireless Headphones', 'price' => 14999],
    ['name' => 'Portable 2TB NVMe External SSD', 'price' => 12499],
    ['name' => 'Smart Fitness Watch Series 9', 'price' => 19999],
    ['name' => 'Multi-Port USB-C Thunderbolt 4 Hub', 'price' => 4599],
    ['name' => 'Full HD 1080p Streaming Webcam', 'price' => 5299],
    ['name' => 'Ergonomic Mesh High-Back Office Chair', 'price' => 18500],
    ['name' => 'Studio Condenser USB Microphone Kit', 'price' => 6999],
];

foreach ($products as $p) {
    \App\Models\Product::updateOrCreate(['name' => $p['name']], $p);
}

echo "Total Products Count: " . \App\Models\Product::count() . "\n";

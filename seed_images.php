<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = \App\Models\Product::all();
foreach($products as $p) {
    $p->image = 'https://placehold.co/400x300/e2e8f0/475569?text='.urlencode($p->name);
    $p->save();
}
echo "Done.\n";

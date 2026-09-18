<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\Product::find(6);
if ($p) {
    $p->image = 'https://file.hstatic.net/200000891321/file/banh-croissant-mua-o-dau_a7c51ffd19dd4bd8a900efaa9d02d783_grande.jpg';
    $p->save();
}
echo "Updated image.\n";

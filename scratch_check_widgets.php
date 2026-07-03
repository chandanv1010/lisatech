<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$widgets = App\Models\Widget::all();
foreach ($widgets as $w) {
    echo "ID: " . $w->id . " - Name: " . $w->name . " - Keyword: " . $w->keyword . " - Model: " . $w->model . "\n";
    echo "Album: " . json_encode($w->album) . "\n";
    echo "Description: " . json_encode($w->description) . "\n";
    echo "---------------------------------\n";
}

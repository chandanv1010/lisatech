<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$systems = DB::table('systems')->get();
foreach ($systems as $sys) {
    echo $sys->keyword . " => " . $sys->content . "\n";
}

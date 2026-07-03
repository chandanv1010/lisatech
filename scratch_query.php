<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Post Catalogues containing 'Vì sao chọn' ---\n";
$cats = App\Models\PostCatalogue::whereHas('languages', function($q) {
    $q->where('post_catalogue_language.name', 'like', '%vì sao%');
})->with('languages')->get();

foreach ($cats as $cat) {
    echo "Cat ID: " . $cat->id . " - Name: " . $cat->languages->first()->pivot->name . "\n";
}

echo "\n--- Posts of current why-lisatech widget ---\n";
$posts = App\Models\Post::whereIn('id', [1341, 1342, 1343, 1344, 1345, 1346])->with('languages')->get();
foreach ($posts as $post) {
    echo "Post ID: " . $post->id . " - Title: " . $post->languages->first()->pivot->name . " - Image: " . $post->image . "\n";
}

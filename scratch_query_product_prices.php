<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Repositories\Product\ProductCatalogueRepository;
use App\Services\V1\Product\ProductCatalogueService;
use App\Services\V1\Product\ProductService;

try {
    $productCatalogueRepository = app(ProductCatalogueRepository::class);
    $productService = app(ProductService::class);
    
    $productCatalogue = $productCatalogueRepository->getProductCatalogueById(74, 1);
    
    $request = request();
    $products = $productService->paginate(
        $request,
        1,
        $productCatalogue,
        1,
        ['path' => $productCatalogue->canonical, 'perPage' => 18],
    );
    
    // Inline private method combineProductValues logic
    $productId = $products->pluck('id')->toArray();
    if (count($productId) && !is_null($productId)) {
        $products = $productService->combineProductAndPromotion($productId, $products);
        $products = $productService->combineProductRelation($products);
    }
    
    $productsList = $products->items();
    echo "Total products: " . count($productsList) . "\n";
    if (count($productsList) > 0) {
        foreach ($productsList as $p) {
             // Let's dump the array representations and object representations
             $arr = $p->toArray();
             echo "Product ID: {$p->id} | Name: {$p->name} | Price in Model: " . var_export($p->price, true) . " | Price in array: " . var_export($arr['price'] ?? null, true) . "\n";
        }
    } else {
        echo "No products found in category 74!\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

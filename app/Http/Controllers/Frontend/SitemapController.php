<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCatalogue;
use App\Models\Post;
use App\Models\PostCatalogue;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        // 1. Static Pages
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => date('Y-m-d'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        $urls[] = [
            'loc' => route('contact.index'),
            'lastmod' => date('Y-m-d'),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];

        // 2. Product Catalogues
        $productCatalogues = ProductCatalogue::where('publish', 2)->with('languages')->get();
        foreach ($productCatalogues as $cat) {
            $pivot = $cat->languages->first()?->pivot;
            if ($pivot && !empty($pivot->canonical)) {
                $urls[] = [
                    'loc' => write_url($pivot->canonical, true, true),
                    'lastmod' => $cat->updated_at ? $cat->updated_at->format('Y-m-d') : date('Y-m-d'),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }
        }

        // 3. Products
        $products = Product::where('publish', 2)->with('languages')->get();
        foreach ($products as $prod) {
            $pivot = $prod->languages->first()?->pivot;
            if ($pivot && !empty($pivot->canonical)) {
                $urls[] = [
                    'loc' => write_url($pivot->canonical, true, true),
                    'lastmod' => $prod->updated_at ? $prod->updated_at->format('Y-m-d') : date('Y-m-d'),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        }

        // 4. Post Catalogues
        $postCatalogues = PostCatalogue::where('pubish', 2)->with('languages')->get();
        foreach ($postCatalogues as $cat) {
            $pivot = $cat->languages->first()?->pivot;
            if ($pivot && !empty($pivot->canonical)) {
                $urls[] = [
                    'loc' => write_url($pivot->canonical, true, true),
                    'lastmod' => $cat->updated_at ? $cat->updated_at->format('Y-m-d') : date('Y-m-d'),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }
        }

        // 5. Posts
        $posts = Post::where('pubish', 2)->with('languages')->get();
        foreach ($posts as $post) {
            $pivot = $post->languages->first()?->pivot;
            if ($pivot && !empty($pivot->canonical)) {
                $urls[] = [
                    'loc' => write_url($pivot->canonical, true, true),
                    'lastmod' => $post->updated_at ? $post->updated_at->format('Y-m-d') : date('Y-m-d'),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }
        }

        // Generate XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $xml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $xml .= '<priority>' . $url['priority'] . '</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}

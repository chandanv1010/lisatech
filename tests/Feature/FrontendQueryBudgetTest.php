<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Query budgets for the frontend page types.
 *
 * The ceilings sit a little above what each page issues today, so ordinary content
 * changes do not trip them but a reintroduced duplicate load, or an N+1 inside a
 * loop, does.
 *
 * Calibrate against THIS suite, not against a browser request: the testing
 * environment uses the array cache driver, so lookups that a warm production
 * request serves from cache are repeated here. The numbers are therefore higher
 * than what the site actually issues. Raise a ceiling only with a reason recorded
 * in the commit — never just to turn a failure green.
 */
class FrontendQueryBudgetTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array{0:int,1:int} [total queries, statements repeated verbatim]
     */
    private function measure(string $url): array
    {
        $seen = [];
        $duplicates = 0;
        $total = 0;

        DB::listen(function ($q) use (&$seen, &$duplicates, &$total) {
            $total++;
            $key = $q->sql.'|'.json_encode($q->bindings);
            if (isset($seen[$key])) {
                $duplicates++;
            }
            $seen[$key] = true;
        });

        $this->get($url)->assertStatus(200);

        return [$total, $duplicates];
    }

    /** @return array<string, array{0:string,1:int,2:int}> */
    public static function pages(): array
    {
        // [url, max queries, max repeated statements] — measured + headroom
        return [
            'homepage' => ['/', 50, 6],
            'post catalogue' => ['/bai-viet/tu-van/c59.html', 68, 3],
            'post detail' => ['/dieu-can-biet-de-lap-dieu-hoa-cho-thang-may-a1253.html', 32, 3],
            'product catalogue' => ['/ac-quy-vien-thong.html', 26, 3],
            'product detail' => ['/bo-luu-dien-ups-6kva-online-1-1-delta-cl-6000vb-p1299.html', 33, 4],
            'contact' => ['/lien-he.html', 8, 1],
        ];
    }

    /** @dataProvider pages */
    public function test_page_stays_within_its_query_budget(string $url, int $maxQueries, int $maxDuplicates): void
    {
        [$total, $duplicates] = $this->measure($url);

        $this->assertLessThanOrEqual(
            $maxQueries,
            $total,
            "$url issued $total queries, budget is $maxQueries"
        );
        $this->assertLessThanOrEqual(
            $maxDuplicates,
            $duplicates,
            "$url repeated $duplicates identical statements, budget is $maxDuplicates"
        );
    }

    /**
     * Site settings are read once per language per request. RouterController calls
     * setSystem() on the controller it resolves by hand, after the middleware has
     * already called it, so without memoisation every page read the whole systems
     * table twice.
     */
    public function test_system_settings_are_read_once_per_request(): void
    {
        $systemQueries = 0;

        DB::listen(function ($q) use (&$systemQueries) {
            if (str_contains($q->sql, 'from `systems`')) {
                $systemQueries++;
            }
        });

        // A page routed through RouterController, which is where the second read
        // used to come from.
        $this->get('/ac-quy-vien-thong.html')->assertStatus(200);

        $this->assertSame(1, $systemQueries, 'systems table should be read exactly once');
    }

    /**
     * Two full loads of product_catalogues built trees that no view ever read.
     * This pins the controllers so they do not come back.
     */
    public function test_controllers_no_longer_build_unused_catalogue_trees(): void
    {
        $catalogue = file_get_contents(app_path('Http/Controllers/Frontend/ProductCatalogueController.php'));
        $product = file_get_contents(app_path('Http/Controllers/Frontend/ProductController.php'));

        $strip = fn (string $s) => preg_replace(['#/\*.*?\*/#s', '#//[^\n]*#'], '', $s);

        $this->assertStringNotContainsString('getChildren()', $strip($catalogue));
        $this->assertStringNotContainsString('descendantTrees', $strip($catalogue));
        $this->assertStringNotContainsString("categorySelectRaw('product')", $strip($product));
    }
}

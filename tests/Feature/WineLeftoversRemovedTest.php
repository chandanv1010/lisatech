<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\V1\Core\CartService;
use App\Services\V1\Product\CompareService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * The wine shop this codebase was reused from left behind a "combo price for 5
 * bottles" rule and several bottle-specific labels. These tests keep them out and
 * check that removing them did not take the cart down with them — CartService's
 * price-sync helper is called from add, update-qty and remove.
 */
class WineLeftoversRemovedTest extends TestCase
{
    use DatabaseTransactions;

    public function test_combo_price_logic_is_gone_from_the_cart_service(): void
    {
        $this->assertFalse(
            method_exists(CartService::class, 'applyComboPrice'),
            'applyComboPrice() should have been replaced by syncCartItemPrices()'
        );
        $this->assertTrue(method_exists(CartService::class, 'syncCartItemPrices'));

        $source = file_get_contents(app_path('Services/V1/Core/CartService.php'));
        // Strip comments so the explanatory note about the removal does not count.
        $code = preg_replace(['#/\*.*?\*/#s', '#//[^\n]*#'], '', $source);
        $this->assertStringNotContainsString('combo_price', $code);
    }

    /** The renamed helper must still be wired into all three cart mutations. */
    public function test_cart_mutations_still_call_the_price_sync(): void
    {
        $source = file_get_contents(app_path('Services/V1/Core/CartService.php'));
        $this->assertSame(3, substr_count($source, '$this->syncCartItemPrices();'));
    }

    /** Calling it on an empty cart must not blow up. */
    public function test_price_sync_runs_on_an_empty_cart(): void
    {
        app(CartService::class)->syncCartItemPrices();
        $this->assertTrue(true);
    }

    public function test_compare_table_has_no_bottle_fields(): void
    {
        $keys = array_column(app(CompareService::class)->fields(), 'key');

        $this->assertNotContains('ml', $keys);
        $this->assertNotContains('percent', $keys);
        // The columns that do apply must survive.
        $this->assertContains('price', $keys);
        $this->assertContains('made_in', $keys);
        $this->assertContains('warranty', $keys);
    }

    public function test_compare_page_still_renders(): void
    {
        $this->get('/so-sanh.html')->assertStatus(200);
    }

    /**
     * The cart sits behind the customer_auth middleware, so a guest is sent to the
     * login page. That is pre-existing behaviour — asserted here so a real 500 from
     * the CartService edit would still be caught rather than mistaken for the
     * redirect.
     */
    public function test_cart_page_redirects_a_guest_to_login(): void
    {
        $this->get('/gio-hang.html')->assertRedirect('/dang-nhap.html');
    }

    /** The product filter cards must not print a bare "ml" / "%" any more. */
    public function test_product_filter_cards_have_no_wine_badge(): void
    {
        $res = $this->get('/ajax/product/filter');

        $res->assertStatus(200);
        $this->assertStringNotContainsString('wine-info', $res->getContent());
        // Still returning real product cards, not an empty list.
        $this->assertStringContainsString('product-item', $res->getContent());
    }

    public function test_products_carry_no_bottle_data_so_nothing_was_lost(): void
    {
        $this->assertSame(0, Product::whereNotNull('ml')->where('ml', '<>', '')->count());
        $this->assertSame(0, Product::whereNotNull('percent')->where('percent', '<>', '')->count());
        $this->assertSame(0, Product::where('combo_price', '>', 0)->count());
    }
}

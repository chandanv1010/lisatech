<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Guards the sidebar entries that were hidden on request and the wine-shop
 * leftovers removed from the product form, so neither creeps back in.
 */
class AdminUiCleanupTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'nhocnguyenkr@gmail.com')->firstOrFail();
    }

    public function test_hidden_sections_are_absent_from_the_sidebar(): void
    {
        $res = $this->actingAs($this->admin())->get('/product/index');
        $res->assertStatus(200);

        foreach ([
            'attribute/catalogue/index',
            'attribute/index',
            'customer/catalogue/index',
            'customer/index',
            'promotion/index',
            'voucher/index',
            'source/index',
            'review/index',
            'introduce/index',
        ] as $route) {
            $res->assertDontSee($route, false);
        }
    }

    public function test_sections_that_must_stay_are_still_in_the_sidebar(): void
    {
        $res = $this->actingAs($this->admin())->get('/product/index');

        foreach ([
            'product/catalogue/index',
            'product/index',
            'order/index',
            'post/catalogue/index',
            'post/index',
            'contact/index',
            'user/index',
            'slide/index',
            'menu/index',
            'system/index',
        ] as $route) {
            $res->assertSee($route, false);
        }
    }

    public function test_product_form_has_no_wine_fields(): void
    {
        $admin = $this->admin();
        $product = Product::query()->firstOrFail();

        foreach (['/product/create', '/product/'.$product->id.'/edit'] as $url) {
            $res = $this->actingAs($admin)->get($url);
            $res->assertStatus(200);

            $res->assertDontSee('Độ rượu', false);
            $res->assertDontSee('Thể tích', false);
            $res->assertDontSee('Giá combo 5 chai', false);
            $res->assertDontSee('name="percent"', false);
            $res->assertDontSee('name="ml"', false);
            $res->assertDontSee('name="combo_price"', false);

            // The real price field must survive the cleanup.
            $res->assertSee('name="price"', false);
        }
    }
}

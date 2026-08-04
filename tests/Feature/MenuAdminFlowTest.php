<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * The horizontal menu is a hand-curated list in "Quản lý menu"; it is not derived
 * from the product groups. These tests pin down the screens the written guide
 * tells the admin to use, so the guide cannot drift away from the real UI.
 */
class MenuAdminFlowTest extends TestCase
{
    use DatabaseTransactions;

    /** "main" menu position, and the "Sản phẩm" top-level item inside it. */
    private const MAIN_MENU_CATALOGUE_ID = 1;
    private const SAN_PHAM_MENU_ID = 8;

    private function admin(): User
    {
        return User::where('email', 'nhocnguyenkr@gmail.com')->firstOrFail();
    }

    public function test_menu_admin_screens_load(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/menu/index')->assertStatus(200);
        $this->actingAs($admin)->get('/menu/'.self::MAIN_MENU_CATALOGUE_ID.'/edit')->assertStatus(200);
        // The screen the guide points at for adding items under "Sản phẩm".
        $this->actingAs($admin)->get('/menu/'.self::SAN_PHAM_MENU_ID.'/children')->assertStatus(200);
    }

    /** The product-group picker must actually find the Soji group. */
    public function test_product_catalogue_picker_finds_soji(): void
    {
        $res = $this->actingAs($this->admin())
            ->get('/ajax/dashboard/getMenu?model=ProductCatalogue&keyword=Soji');

        $res->assertStatus(200);
        // assertSee would fail on the response's \uXXXX escaping, so match the
        // decoded JSON instead.
        $res->assertJsonFragment([
            'id' => 1171,
            'name' => 'Bộ lưu điện cửa cuốn 3 pha Soji',
            'canonical' => 'bo-luu-dien-cua-cuon-3-pha-soji-pc1171',
        ]);
    }
}

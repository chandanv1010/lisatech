<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Ô "Ứng dụng" ở trang sửa sản phẩm.
 *
 * Nội dung ghi xuống bảng product_language được, nhưng getProductById lại
 * không chọn cột applications trong danh sách cột của nó. Nên mở lại trang sửa
 * thì CKEditor trắng, và lần lưu kế tiếp ghi đè chính nó bằng chuỗi rỗng - vì
 * vậy trong cơ sở dữ liệu thật không có nổi một bản ghi nào còn dữ liệu.
 */
class ProductApplicationsFieldTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'nhocnguyenkr@gmail.com')->firstOrFail();
    }

    private function sanPham(): object
    {
        return DB::table('products')
            ->join('product_language as pl', 'pl.product_id', '=', 'products.id')
            ->where('pl.language_id', 1)
            ->whereNull('products.deleted_at')
            ->select('products.id', 'products.product_catalogue_id', 'pl.name', 'pl.canonical')
            ->first();
    }

    private function duLieuLuu(object $sp, string $ungDung): array
    {
        return [
            'name' => $sp->name,
            'canonical' => $sp->canonical,
            'product_catalogue_id' => $sp->product_catalogue_id ?: 1,
            'description' => '',
            'content' => '',
            'applications' => $ungDung,
            'meta_title' => '',
            'meta_keyword' => '',
            'meta_description' => '',
            'publish' => 2,
            'follow' => 2,
            // ProductService đọc thẳng các khoá này khỏi request và ngã nếu
            // thiếu, nên phải gửi đủ như form thật vẫn gửi.
            'price' => 0,
            'stock' => 0,
            'code' => '',
            'made_in' => '',
            'image' => '',
            'download' => '',
            'album' => '',
            'attributeCatalogue' => '',
            'variant' => '',
            'iframe' => '',
            'guarantee' => '',
            'total_lesson' => 0,
            'duration' => '',
            'chapter' => '',
            'ml' => '',
            'percent' => '',
            'lession_content' => '',
        ];
    }

    public function test_truy_van_lay_san_pham_co_chon_cot_applications(): void
    {
        // Kiểm thẳng vào nguyên nhân: nếu ai đó dọn lại danh sách cột trong
        // tương lai thì hỏng ở đây, chứ không hỏng lặng lẽ ngoài production.
        $sp = $this->sanPham();
        $noiDung = '<p>Ứng dụng kiểm thử ' . uniqid() . '</p>';

        DB::table('product_language')
            ->where('product_id', $sp->id)->where('language_id', 1)
            ->update(['applications' => $noiDung]);

        $doc = app(\App\Repositories\Product\ProductRepository::class)->getProductById($sp->id, 1);

        $this->assertSame($noiDung, $doc->applications);
    }

    public function test_luu_roi_mo_lai_thi_noi_dung_ung_dung_van_con(): void
    {
        $sp = $this->sanPham();
        $noiDung = '<p>Ứng dụng: thang máy tải khách ' . uniqid() . '</p>';

        $this->actingAs($this->admin())
            ->post("/product/{$sp->id}/update", $this->duLieuLuu($sp, $noiDung))
            ->assertRedirect();

        // Đã ghi xuống cơ sở dữ liệu chưa
        $luu = DB::table('product_language')
            ->where('product_id', $sp->id)->where('language_id', 1)
            ->value('applications');

        $this->assertSame($noiDung, $luu, 'Nội dung Ứng dụng không được lưu');

        // Và mở lại trang sửa thì có đổ ngược vào ô nhập không
        $res = $this->actingAs($this->admin())->get("/product/{$sp->id}/edit");

        $res->assertStatus(200);
        $res->assertSee('name="applications"', false);
        $res->assertSee(e($noiDung), false);
    }

    public function test_luu_lan_hai_khong_lam_mat_noi_dung_da_co(): void
    {
        // Đây là cách dữ liệu thật bị xoá sạch: ô hiện trắng vì không đọc lên
        // được, người dùng bấm lưu vì việc khác, thế là chuỗi rỗng đè lên.
        $sp = $this->sanPham();
        $noiDung = '<p>Nội dung phải sống sót</p>';

        $this->actingAs($this->admin())
            ->post("/product/{$sp->id}/update", $this->duLieuLuu($sp, $noiDung))
            ->assertRedirect();

        $res = $this->actingAs($this->admin())->get("/product/{$sp->id}/edit");
        $res->assertSee(e($noiDung), false);

        // Lưu lại đúng những gì trang sửa đang hiển thị
        $this->actingAs($this->admin())
            ->post("/product/{$sp->id}/update", $this->duLieuLuu($sp, $noiDung))
            ->assertRedirect();

        $this->assertSame($noiDung, DB::table('product_language')
            ->where('product_id', $sp->id)->where('language_id', 1)
            ->value('applications'));
    }
}

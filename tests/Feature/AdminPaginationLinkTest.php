<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Liên kết phân trang trong trang quản trị.
 *
 * BaseRepository nối chuỗi env('APP_URL') với đường dẫn mà không có dấu gạch
 * chéo, cho ra "http://host:8001contact/index?page=2". Mọi nút chuyển trang
 * của cả 39 trang danh sách trong quản trị đều dẫn tới hư không.
 *
 * Còn một hệ quả nữa chỉ lộ ra trên máy chủ thật: env() đọc lúc chạy sẽ trả
 * null khi cấu hình đã được cache, lúc đó liên kết mất luôn cả tên miền.
 */
class AdminPaginationLinkTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'nhocnguyenkr@gmail.com')->firstOrFail();
    }

    public function test_duong_dan_phan_trang_co_dau_gach_cheo(): void
    {
        $res = $this->actingAs($this->admin())->get('/contact/index');
        $res->assertStatus(200);

        $contacts = $res->viewData('contacts');

        $this->assertStringContainsString('/contact/index', $contacts->path());
        // Đây chính là dạng hỏng: tên miền dính liền vào đường dẫn.
        $this->assertStringNotContainsString('8001contact', $contacts->path());
        $this->assertMatchesRegularExpression('#^https?://[^/]+/contact/index$#', $contacts->path());
    }

    public function test_bam_sang_trang_hai_ra_ban_ghi_khac(): void
    {
        $trang1 = $this->actingAs($this->admin())->get('/contact/index');
        $trang2 = $this->actingAs($this->admin())->get('/contact/index?page=2');

        $trang1->assertStatus(200);
        $trang2->assertStatus(200);

        $id1 = $trang1->viewData('contacts')->getCollection()->pluck('id')->all();
        $id2 = $trang2->viewData('contacts')->getCollection()->pluck('id')->all();

        $this->assertNotEmpty($id2, 'Trang 2 phải có bản ghi');
        $this->assertEmpty(array_intersect($id1, $id2), 'Hai trang không được trùng bản ghi');
    }

    public function test_lien_ket_trong_html_tro_dung_dia_chi(): void
    {
        $res = $this->actingAs($this->admin())->get('/contact/index');

        $res->assertStatus(200);
        $res->assertSee('/contact/index?page=2', false);
        $res->assertDontSee('8001contact/index', false);
    }
}

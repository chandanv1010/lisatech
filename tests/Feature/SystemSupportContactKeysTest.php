<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Khối "Hỗ trợ trực tuyến" trong Cấu hình hệ thống.
 *
 * Form quản trị dựng tên trường bằng {nhóm}_{khoá}. Nhóm tên là 'support', mà
 * các khoá bên trong lại đã mang sẵn tiền tố 'support_', nên form gửi lên
 * config[support_support_phone_1] trong khi giao diện ngoài đọc
 * $system['support_phone_' . $i]. Sửa bao nhiêu lần ngoài trang cũng không đổi.
 */
class SystemSupportContactKeysTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'nhocnguyenkr@gmail.com')->firstOrFail();
    }

    public function test_khoa_cau_hinh_khong_bi_lap_tien_to(): void
    {
        foreach ((new \App\Classes\System())->config() as $nhom => $val) {
            foreach (array_keys($val['value'] ?? []) as $truong) {
                $this->assertFalse(
                    str_starts_with($truong, $nhom . '_'),
                    "Khoá '{$truong}' đã mang sẵn tiền tố của nhóm '{$nhom}', "
                    . "form sẽ sinh ra '{$nhom}_{$truong}' và không khớp chỗ nào đọc"
                );
            }
        }
    }

    public function test_form_quan_tri_sinh_dung_ten_truong_ma_giao_dien_doc(): void
    {
        $res = $this->actingAs($this->admin())->get('/system/index');

        $res->assertStatus(200);

        // Đúng tên mà $system['support_phone_' . $i] sẽ đọc.
        $res->assertSee('name="config[support_phone_1]"', false);
        $res->assertSee('name="config[support_name_1]"', false);
        $res->assertSee('name="config[support_zalo_1]"', false);

        // Và không còn tên lặp tiền tố.
        $res->assertDontSee('config[support_support_', false);
    }

    public function test_luu_hotline_ho_tro_thi_ghi_vao_dung_khoa(): void
    {
        $soMoi = '0900 111 ' . random_int(1000, 9999);

        $this->actingAs($this->admin())
            ->post('/system/store', ['config' => ['support_phone_1' => $soMoi]])
            ->assertRedirect();

        $luu = DB::table('systems')
            ->where('language_id', 1)
            ->where('keyword', 'support_phone_1')
            ->value('content');

        $this->assertSame($soMoi, $luu);

        // Và không đẻ ra khoá lặp.
        $this->assertSame(
            0,
            DB::table('systems')->where('keyword', 'like', 'support_support_%')->count()
        );
    }

    public function test_sua_xong_thi_giao_dien_ngoai_hien_theo(): void
    {
        $soMoi = '0911 222 ' . random_int(1000, 9999);

        DB::table('systems')->updateOrInsert(
            ['keyword' => 'support_phone_1', 'language_id' => 1],
            ['content' => $soMoi, 'user_id' => $this->admin()->id]
        );
        DB::table('systems')->updateOrInsert(
            ['keyword' => 'support_name_1', 'language_id' => 1],
            ['content' => 'Hỗ trợ kiểm thử', 'user_id' => $this->admin()->id]
        );

        // Khối này hiện ở sidebar trang danh mục sản phẩm.
        $danhMuc = DB::table('product_catalogues as pc')
            ->join('product_catalogue_language as pl', 'pl.product_catalogue_id', '=', 'pc.id')
            ->join('routers as r', function ($join) {
                $join->on('r.module_id', '=', 'pc.id')
                    ->where('r.controllers', 'App\Http\Controllers\Frontend\ProductCatalogueController');
            })
            ->where('pl.language_id', 1)
            ->whereNull('pc.deleted_at')
            ->select('pl.canonical')
            ->first();

        if ($danhMuc === null) {
            $this->markTestSkipped('Cần một danh mục sản phẩm có đường dẫn.');
        }

        $res = $this->get('/' . $danhMuc->canonical . config('apps.general.suffix'));

        $res->assertStatus(200);
        $res->assertSee($soMoi, false);
    }
}

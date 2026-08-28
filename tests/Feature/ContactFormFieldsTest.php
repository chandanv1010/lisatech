<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Hai lỗi được báo về từ trang chủ:
 *
 *  1. Email khách gửi lên không được lưu. Nguyên nhân: cột 'email' có thật
 *     trong bảng và saveContact có truyền lên, nhưng thiếu trong $fillable nên
 *     gán hàng loạt lặng lẽ bỏ đi. Không lỗi, không cảnh báo, chỉ mất dữ liệu.
 *
 *  2. Nội dung tư vấn hiện ở cột Địa chỉ trong trang quản trị. Nguyên nhân:
 *     form tư vấn ở trang sản phẩm nhồi chuỗi "Yêu cầu tư vấn sản phẩm: ..."
 *     vào trường address, trong khi bảng đã có sẵn cột product_id.
 */
class ContactFormFieldsTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'nhocnguyenkr@gmail.com')->firstOrFail();
    }

    private function sanPhamDauTien(): ?object
    {
        return DB::table('products')
            ->join('product_language as pl', 'pl.product_id', '=', 'products.id')
            ->where('pl.language_id', 1)
            ->select('products.id', 'pl.name')
            ->first();
    }

    public function test_email_khach_gui_len_phai_duoc_luu(): void
    {
        $email = 'kiemthu-' . uniqid() . '@test.local';

        $this->post('/lien-he.html', [
            'name' => 'Khách kiểm thử email',
            'phone' => '0900555666',
            'email' => $email,
            'message' => 'Nội dung liên hệ',
        ])->assertRedirect();

        $contact = Contact::where('phone', '0900555666')->latest('id')->first();

        $this->assertNotNull($contact, 'Liên hệ phải được tạo');
        $this->assertSame($email, $contact->email, 'Email bị bỏ mất khi lưu');
    }

    public function test_email_nam_trong_fillable(): void
    {
        // Kiểm thẳng vào nguyên nhân, để nếu ai đó dọn $fillable trong tương lai
        // thì hỏng ở đây chứ không hỏng lặng lẽ ngoài production.
        $this->assertContains('email', (new Contact())->getFillable());
    }

    public function test_form_tu_van_luu_san_pham_vao_dung_cot(): void
    {
        $sanPham = $this->sanPhamDauTien();
        $this->assertNotNull($sanPham, 'Cần ít nhất một sản phẩm');

        $this->post('/lien-he.html', [
            'name' => 'Khách hỏi tư vấn',
            'phone' => '0900777888',
            'email' => 'tuvan@test.local',
            'message' => 'Tôi muốn được tư vấn',
            'product_id' => $sanPham->id,
        ])->assertRedirect();

        $contact = Contact::where('phone', '0900777888')->latest('id')->first();

        $this->assertNotNull($contact);
        $this->assertSame((int) $sanPham->id, (int) $contact->product_id);
        // Địa chỉ phải để trống, không bị mượn làm chỗ chứa nội dung tư vấn.
        $this->assertEmpty($contact->address);
    }

    public function test_form_tu_van_o_trang_san_pham_khong_con_nhoi_vao_address(): void
    {
        $view = file_get_contents(resource_path('views/frontend/product/product/index.blade.php'));

        $this->assertStringNotContainsString(
            'name="address"',
            $view,
            'Form tư vấn không được gửi trường address ẩn nữa'
        );
        $this->assertStringContainsString('name="product_id"', $view);
    }

    public function test_danh_sach_quan_tri_hien_ten_san_pham_thay_vi_de_o_cot_dia_chi(): void
    {
        $sanPham = $this->sanPhamDauTien();

        Contact::create([
            'name' => 'Khách hỏi tư vấn hiển thị',
            'phone' => '0900999000',
            'email' => 'hienthi@test.local',
            'message' => 'Xin tư vấn',
            'type' => 3,
            'product_id' => $sanPham->id,
            'status' => (int) config('apps.general.contactStatusPending'),
        ]);

        $res = $this->actingAs($this->admin())->get('/contact/index');

        $res->assertStatus(200);
        $res->assertSee('Khách hỏi tư vấn hiển thị', false);
        $res->assertSee($sanPham->name, false);
        // Cột Địa chỉ đã bị bỏ khỏi bảng, nội dung tư vấn không còn chỗ để lạc vào.
        $res->assertDontSee('<th>Địa chỉ</th>', false);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Trạng thái xử lý và người xử lý của liên hệ.
 *
 * Chạy trên cơ sở dữ liệu thật đã nhập, nên mọi thao tác ghi đều phải cuộn
 * ngược lại khi test kết thúc.
 */
class ContactHandlingStatusTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'nhocnguyenkr@gmail.com')->firstOrFail();
    }

    private function chuaXuLy(): int
    {
        return (int) config('apps.general.contactStatusPending');
    }

    private function daXuLy(): int
    {
        return (int) config('apps.general.contactStatusDone');
    }

    private function taoLienHe(int $status): Contact
    {
        return Contact::create([
            'name' => 'Khách kiểm thử ' . uniqid(),
            'phone' => '0900000000',
            'address' => 'Hà Nội',
            'message' => 'Nội dung kiểm thử',
            'type' => 3,
            'status' => $status,
        ]);
    }

    public function test_trang_danh_sach_hien_hai_cot_moi(): void
    {
        $res = $this->actingAs($this->admin())->get('/contact/index');

        $res->assertStatus(200);
        $res->assertSee('Trạng thái', false);
        $res->assertSee('Người xử lý', false);
        $res->assertSee('Chưa xử lý', false);
    }

    public function test_lien_he_chua_xu_ly_duoc_xep_len_dau(): void
    {
        // Bản ghi đã xử lý này có id lớn nhất, nên nếu sắp xếp chỉ theo id thì
        // nó sẽ đứng đầu. Đúng ra nó phải bị đẩy xuống dưới nhóm chưa xử lý.
        $daXong = $this->taoLienHe($this->daXuLy());

        $res = $this->actingAs($this->admin())->get('/contact/index');
        $res->assertStatus(200);

        $contacts = $res->viewData('contacts');
        $danhSach = $contacts->getCollection();

        $this->assertGreaterThan(0, $danhSach->count());

        // Trang đầu phải toàn là chưa xử lý, chừng nào còn liên hệ chưa xử lý.
        $trangThai = $danhSach->pluck('status')->map(fn ($s) => (int) $s)->all();
        $daSapXep = $trangThai;
        sort($daSapXep);

        $this->assertSame(
            $daSapXep,
            $trangThai,
            'Trạng thái phải tăng dần, tức là chưa xử lý nằm trên đã xử lý'
        );

        $this->assertNotSame(
            $daXong->id,
            (int) $danhSach->first()->id,
            'Bản ghi đã xử lý không được đứng đầu dù nó mới nhất'
        );
    }

    public function test_trong_cung_nhom_thi_moi_nhat_len_truoc(): void
    {
        $res = $this->actingAs($this->admin())->get('/contact/index');
        $danhSach = $res->viewData('contacts')->getCollection();

        $theoNhom = $danhSach->groupBy('status');

        foreach ($theoNhom as $nhom) {
            $ids = $nhom->pluck('id')->map(fn ($i) => (int) $i)->all();
            $giamDan = $ids;
            rsort($giamDan);

            $this->assertSame($giamDan, $ids, 'Trong một nhóm trạng thái, id phải giảm dần');
        }
    }

    public function test_danh_dau_da_xu_ly_thi_ghi_lai_nguoi_bam(): void
    {
        $admin = $this->admin();
        $contact = $this->taoLienHe($this->chuaXuLy());

        $res = $this->actingAs($admin)->postJson('/ajax/contact/updateStatus', [
            'id' => $contact->id,
            'status' => $this->daXuLy(),
        ]);

        $res->assertStatus(200);
        $res->assertJsonPath('data.status', $this->daXuLy());
        $res->assertJsonPath('data.handler_name', $admin->name);

        $contact->refresh();
        $this->assertSame($this->daXuLy(), (int) $contact->status);
        $this->assertSame($admin->id, (int) $contact->handled_by);
    }

    public function test_chuyen_nguoc_ve_chua_xu_ly_thi_xoa_nguoi_xu_ly(): void
    {
        $admin = $this->admin();
        $contact = $this->taoLienHe($this->chuaXuLy());

        $this->actingAs($admin)->postJson('/ajax/contact/updateStatus', [
            'id' => $contact->id, 'status' => $this->daXuLy(),
        ])->assertStatus(200);

        $this->actingAs($admin)->postJson('/ajax/contact/updateStatus', [
            'id' => $contact->id, 'status' => $this->chuaXuLy(),
        ])->assertStatus(200);

        $contact->refresh();
        $this->assertSame($this->chuaXuLy(), (int) $contact->status);
        // Giữ lại tên cũ sẽ thành lời khai sai rằng người đó đang phụ trách.
        $this->assertNull($contact->handled_by);
    }

    public function test_tu_choi_trang_thai_khong_hop_le(): void
    {
        $contact = $this->taoLienHe($this->chuaXuLy());

        $this->actingAs($this->admin())->postJson('/ajax/contact/updateStatus', [
            'id' => $contact->id, 'status' => 99,
        ])->assertStatus(422);

        $contact->refresh();
        $this->assertSame($this->chuaXuLy(), (int) $contact->status);
    }

    public function test_lien_he_khong_ton_tai_thi_bao_loi_chu_khong_no(): void
    {
        $this->actingAs($this->admin())->postJson('/ajax/contact/updateStatus', [
            'id' => 999999999, 'status' => $this->daXuLy(),
        ])->assertStatus(422);
    }

    public function test_chua_dang_nhap_thi_khong_doi_duoc(): void
    {
        $contact = $this->taoLienHe($this->chuaXuLy());

        $this->post('/ajax/contact/updateStatus', [
            'id' => $contact->id, 'status' => $this->daXuLy(),
        ])->assertRedirect();

        $contact->refresh();
        $this->assertSame($this->chuaXuLy(), (int) $contact->status);
    }

    public function test_lien_he_moi_gui_len_mac_dinh_la_chua_xu_ly(): void
    {
        $this->post('/lien-he.html', [
            'name' => 'Khách mới kiểm thử',
            'phone' => '0900000123',
            'email' => 'moi@test.local',
            'message' => 'Xin tư vấn',
            'type' => 3,
        ])->assertRedirect();

        $contact = Contact::where('phone', '0900000123')->latest('id')->first();

        $this->assertNotNull($contact);
        $this->assertSame($this->chuaXuLy(), (int) $contact->status);
        $this->assertNull($contact->handled_by);
    }
}

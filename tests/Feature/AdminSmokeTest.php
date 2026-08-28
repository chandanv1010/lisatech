<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    // DatabaseTransactions, never RefreshDatabase: this suite runs against the
    // real imported database, so every write has to roll back when the test ends.
    use DatabaseTransactions;

    private function admin(): User
    {
        // An existing, non-deleted "Quản trị viên" account. Only read, never written.
        return User::where('email', 'nhocnguyenkr@gmail.com')->firstOrFail();
    }

    /** Bug 1 — "Quản lý liên hệ" used to 500 on "Unknown column 'publish'". */
    public function test_contact_index_loads_and_lists_contacts(): void
    {
        // Lấy tên theo đúng thứ tự mà trang dùng, thay vì ghi cứng một cái tên.
        // Danh sách giờ xếp liên hệ chưa xử lý lên đầu, nên bất kỳ ai bấm
        // "đã xử lý" cho một liên hệ là nó rời trang một - và một bài test ghi
        // cứng tên sẽ đỏ lên vì tính năng chạy đúng, chứ không phải vì hỏng.
        $dauTien = DB::table('contacts')
            ->whereNull('deleted_at')
            ->orderBy('status')
            ->orderByDesc('id')
            ->value('name');

        $res = $this->actingAs($this->admin())->get('/contact/index');

        $res->assertStatus(200);
        // Một liên hệ có thật phải hiện ra trên trang.
        $this->assertNotNull($dauTien);
        $res->assertSee($dauTien, false);
        // And the admin must be able to tell the channel apart at a glance.
        $res->assertSee('Loại', false);
        $res->assertSee('Website cũ', false);
    }

    /** Bug 1 — a quote request has to be distinguishable from a sales enquiry. */
    public function test_quote_and_business_contacts_are_saved_with_their_type(): void
    {
        $quote = (int) config('apps.general.contactTypeQuote');
        $business = (int) config('apps.general.contactTypeBusiness');

        $this->post('/lien-he.html', [
            'name' => 'Test Báo Giá', 'phone' => '0900000001',
            'email' => 'quote@test.local', 'message' => 'Xin báo giá',
            'type' => $quote,
        ])->assertRedirect();

        $this->post('/lien-he.html', [
            'name' => 'Test Kinh Doanh', 'phone' => '0900000002',
            'email' => 'biz@test.local', 'message' => 'Hợp tác',
            'type' => $business,
        ])->assertRedirect();

        $this->assertSame($quote, (int) Contact::where('phone', '0900000001')->value('type'));
        $this->assertSame($business, (int) Contact::where('phone', '0900000002')->value('type'));
    }

    /** A form that posts no type at all must not fall back to NULL. */
    public function test_contact_without_type_defaults_to_business(): void
    {
        $this->post('/lien-he.html', [
            'name' => 'Không Có Type', 'phone' => '0900000003',
            'email' => 'notype@test.local', 'message' => 'Test',
        ])->assertRedirect();

        $this->assertSame(
            (int) config('apps.general.contactTypeBusiness'),
            (int) Contact::where('phone', '0900000003')->value('type')
        );
    }
}

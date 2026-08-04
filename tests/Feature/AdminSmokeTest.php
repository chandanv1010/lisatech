<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
        $res = $this->actingAs($this->admin())->get('/contact/index');

        $res->assertStatus(200);
        // The newest real submission must actually be on the page.
        $res->assertSee('Trần Văn An', false);
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

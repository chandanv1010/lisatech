<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostCatalogue;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * post_catalogues and posts kept their original mis-spelled column names
 * (`parentid`, `pubish`). The services rename the payload keys to match, but
 * Eloquent then has to be allowed to mass-assign those names — otherwise the
 * values are silently dropped and the row falls back to its column default:
 * parentid = 0 (looks like a root group) and pubish = 1 ("Không xuất bản").
 */
class LegacyColumnPayloadTest extends TestCase
{
    use DatabaseTransactions;

    private const LINH_VUC_ID = 58;

    private function admin(): User
    {
        return User::where('email', 'nhocnguyenkr@gmail.com')->firstOrFail();
    }

    /** Bug 3 — a child group must keep the parent it was saved with. */
    public function test_post_catalogue_keeps_its_parent(): void
    {
        $res = $this->actingAs($this->admin())->post('/post/catalogue/store', [
            'name' => 'Cảng biển đặc thù',
            'canonical' => 'cang-bien-dac-thu-test-'.self::LINH_VUC_ID,
            'parent_id' => self::LINH_VUC_ID,
            'publish' => 2,
            'follow' => 2,
            'description' => '', 'content' => '',
            'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '',
        ]);
        $res->assertRedirect();

        $row = DB::table('post_catalogues')->orderByDesc('id')->first();
        $this->assertSame(self::LINH_VUC_ID, (int) $row->parentid, 'parentid was not persisted');
        $this->assertSame(2, (int) $row->pubish, 'pubish was not persisted');

        // The nested-set rebuild has to place it one level below "Lĩnh vực",
        // otherwise the admin tree still draws it as a top-level group.
        $parentLevel = (int) DB::table('post_catalogues')->where('id', self::LINH_VUC_ID)->value('level');
        $this->assertSame($parentLevel + 1, (int) $row->level, 'level was not recalculated under the parent');
    }

    /** Bug 4 — a post saved as "Hoạt động" must not come back inactive. */
    public function test_post_keeps_its_publish_state(): void
    {
        $catalogue = PostCatalogue::query()->firstOrFail();

        $res = $this->actingAs($this->admin())->post('/post/store', [
            'name' => 'HÃNG SCHEINER STEUERUNGSTECHNIK CMBH GIỚI THIỆU LISA21',
            'canonical' => 'hang-scheiner-gioi-thieu-lisa21-test',
            'post_catalogue_id' => $catalogue->id,
            'publish' => 2,
            'follow' => 2,
            'description' => '', 'content' => '',
            'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '',
        ]);
        $res->assertRedirect();

        $row = DB::table('posts')->orderByDesc('id')->first();
        $this->assertSame(2, (int) $row->pubish, 'pubish was not persisted');
    }
}

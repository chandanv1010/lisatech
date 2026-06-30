<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HeroBannerSeeder extends Seeder
{
    private int $languageId = 1;
    private int $userId = 1;

    public function run(): void
    {
        $now = now();
        $this->userId = (int) (DB::table('users')->value('id') ?? 1);

        // 1. Create/Update Menu Catalogue: 'hero-sidebar'
        $catalogueId = DB::table('menu_catalogues')->where('keyword', 'hero-sidebar')->value('id');
        if (!$catalogueId) {
            $catalogueId = DB::table('menu_catalogues')->insertGetId([
                'name' => 'Tìm nhanh danh mục',
                'keyword' => 'hero-sidebar',
                'publish' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('menu_catalogues')->where('id', $catalogueId)->update([
                'name' => 'Tìm nhanh danh mục',
                'publish' => 2,
                'updated_at' => $now,
            ]);
        }

        // Seed menu items
        $menuItems = [
            ['name' => 'Thiết bị, linh kiện thang máy', 'icon' => 'fa fa-building', 'canonical' => '#'],
            ['name' => 'Thiết bị nguồn', 'icon' => 'fa fa-plug', 'canonical' => '#'],
            ['name' => 'Biến tần & Servo Yaskawa - Japan', 'icon' => 'fa fa-microchip', 'canonical' => '#'],
            ['name' => 'Bộ biến đổi tần số 50Hz/60Hz/...', 'icon' => 'fa fa-bolt', 'canonical' => '#'],
            ['name' => 'Bộ lưu điện UPS Riello / Delta', 'icon' => 'fa fa-battery-half', 'canonical' => '#'],
            ['name' => 'Bộ lưu điện cửa cuốn', 'icon' => 'fa fa-columns', 'canonical' => '#'],
            ['name' => 'Ắc quy chính hãng', 'icon' => 'fa fa-car-battery', 'canonical' => '#'],
        ];

        // Clean existing menu items in this catalogue to avoid duplicates
        DB::table('menus')->where('menu_catalogue_id', $catalogueId)->delete();

        foreach ($menuItems as $index => $item) {
            $menuId = DB::table('menus')->insertGetId([
                'parent_id' => 0,
                'menu_catalogue_id' => $catalogueId,
                'lft' => 0,
                'rgt' => 0,
                'level' => 0,
                'type' => 'link',
                'icon' => $item['icon'],
                'publish' => 2,
                'order' => 1000 - $index,
                'user_id' => $this->userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('menu_language')->insert([
                'menu_id' => $menuId,
                'language_id' => $this->languageId,
                'name' => $item['name'],
                'canonical' => $item['canonical'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Create posts for Widget: hero-content
        // Post 1: Main hero content (Sử dụng ảnh nền từ HTML)
        $mainHeroPostId = $this->upsertPost(
            'Giải pháp điều khiển & thiết bị công nghiệp cho bán lẻ, phân phối và dự án',
            'Lisatech - nơi những tài năng Việt làm việc cùng nhau trong một môi trường lành mạnh, không ngừng sáng tạo và cải tiến, một môi trường đủ thử thách để mang tới những sản phẩm và dịch vụ vươn ra toàn cầu.',
            'https://placehold.co/1920x600/e8f0f8/cccccc?text=Thay+Anh+Nen+Thanh+Pho+Vao+Day',
            0
        );

        // Update main hero post's album to hold buttons config
        DB::table('posts')->where('id', $mainHeroPostId)->update([
            'album' => json_encode([
                [
                    'label' => 'Tìm sản phẩm',
                    'url' => '#',
                    'icon' => 'fa fa-shopping-cart'
                ],
                [
                    'label' => 'Yêu cầu báo giá',
                    'url' => '#',
                    'icon' => 'fa fa-paper-plane'
                ]
            ], JSON_UNESCAPED_UNICODE)
        ]);

        // Post 2, 3, 4: Features
        $feat1Id = $this->upsertPost(
            'Hàng chính hãng<br>CO/CQ đầy đủ',
            'Hàng chính hãng CO/CQ đầy đủ',
            '',
            1
        );
        DB::table('posts')->where('id', $feat1Id)->update(['icon' => 'fa fa-check-circle']); // Icon từ HTML

        $feat2Id = $this->upsertPost(
            'Tư vấn kỹ thuật<br>miễn phí',
            'Tư vấn kỹ thuật miễn phí',
            '',
            2
        );
        DB::table('posts')->where('id', $feat2Id)->update(['icon' => 'fa fa-cog']); // Icon từ HTML

        $feat3Id = $this->upsertPost(
            'Giao hàng toàn quốc<br>nhanh chóng',
            'Giao hàng toàn quốc nhanh chóng',
            '',
            3
        );
        DB::table('posts')->where('id', $feat3Id)->update(['icon' => 'fa fa-truck']);

        // 3. Tạo/slide cho HeroBanner - Sử dụng các đường dẫn ảnh thực tế từ trang web
        $this->seedHeroSlides();

        // 4. Link posts to Widget 'hero-content'
        $widgetAlbum = [
            'https://placehold.co/400x350/transparent/333333?text=Anh+San+Pham'
        ];

        $this->upsertWidget(
            'hero-content',
            'Nội dung Hero Banner',
            'Post',
            [$mainHeroPostId, $feat1Id, $feat2Id, $feat3Id],
            'Hero banner main content and features',
            $widgetAlbum // Truyền mảng album chứa ảnh sản phẩm vào widget
        );
    }

    private function upsertPost(string $name, string $description, string $image, int $order): int
    {
        $now = now();
        $postId = DB::table('post_language')
            ->where('language_id', $this->languageId)
            ->where('name', $name)
            ->value('post_id');

        $payload = [
            'post_catalogue_id' => 0,
            'image' => $image,
            'pubish' => 2,
            'order' => 1000 - $order,
            'user_id' => $this->userId,
            'deleted_at' => null,
            'updated_at' => $now,
        ];

        if ($postId) {
            DB::table('posts')->where('id', $postId)->update($payload);
        } else {
            $payload['created_at'] = $now;
            $postId = DB::table('posts')->insertGetId($payload);
        }

        $languagePayload = [
            'name' => $name,
            'description' => $description,
            'content' => $description,
            'meta_title' => strip_tags($name),
            'meta_keyword' => strip_tags($name),
            'meta_description' => strip_tags($description),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if (Schema::hasColumn('post_language', 'canonical')) {
            $currentCanonical = DB::table('post_language')
                ->where('post_id', $postId)
                ->where('language_id', $this->languageId)
                ->value('canonical');

            $languagePayload['canonical'] = $currentCanonical ?: $this->canonicalFromName(strip_tags($name), $postId);
        }

        DB::table('post_language')->updateOrInsert(
            ['post_id' => $postId, 'language_id' => $this->languageId],
            $languagePayload
        );

        return (int) $postId;
    }

    private function canonicalFromName(string $name, int $id): string
    {
        $canonical = Str::slug($name);
        return $canonical !== '' ? "{$canonical}-{$id}" : "post-{$id}";
    }

    // Hàm upsertWidget được cập nhật để có thể nhận thêm array $album cho ảnh sản phẩm
    private function upsertWidget(string $keyword, string $name, string $model, array $modelIds, string $description, array $album = []): void
    {
        $now = now();
        DB::table('widgets')->updateOrInsert(
            ['keyword' => $keyword],
            [
                'name' => $name,
                'description' => json_encode([$this->languageId => $description], JSON_UNESCAPED_UNICODE),
                'album' => json_encode($album, JSON_UNESCAPED_UNICODE), // Lưu URL ảnh sản phẩm vào trường album của Widget
                'model_id' => json_encode(array_values($modelIds)),
                'model' => $model,
                'short_code' => '',
                'publish' => 2,
                'note' => 'Hero banner dynamic widget',
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    private function seedHeroSlides(): void
    {
        // Clean existing hero slides to avoid duplicates
        DB::table('hero_slides')->delete();

        // Create slide data based on the interface design
        // Slide 1: Main hero slide with background image
        DB::table('hero_slides')->insert([
            'title' => 'Giải pháp điều khiển & thiết bị công nghiệp',
            'description' => 'cho bán lẻ, phân phối và dự án',
            'subtitle' => 'cho bán lẻ, phân phối và dự án',
            'background_image' => 'https://placehold.co/1920x600/e8f0f8/cccccc?text=Thay+Anh+Nen+Thanh+Pho+Vao+Day',
            'order' => 1,
            'status' => 'active',
            'user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Slide 2: Product showcase (using the product image from interface)
        DB::table('hero_slides')->insert([
            'title' => 'Sản phẩm chính hãng',
            'description' => 'CO/CQ đầy đủ - Hàng chính hãng',
            'subtitle' => 'Đảm bảo chất lượng và nguồn gốc',
            'background_image' => 'https://placehold.co/400x350/transparent/333333?text=Anh+San+Pham',
            'order' => 2,
            'status' => 'active',
            'user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Slide 3: Free technical consultation (based on features in interface)
        DB::table('hero_slides')->insert([
            'title' => 'Tư vấn kỹ thuật',
            'description' => 'miễn phí từ chuyên gia',
            'subtitle' => 'Hỗ trợ khách hàng 24/7',
            'background_image' => 'https://placehold.co/1920x600/e8f0f8/cccccc?text=Tuvan+Ky+Thuat',
            'order' => 3,
            'status' => 'active',
            'user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Slide 4: Nationwide delivery (based on features in interface)
        DB::table('hero_slides')->insert([
            'title' => 'Giao hàng toàn quốc',
            'description' => 'nhanh chóng và an toàn',
            'subtitle' => 'Giao hàng mọi lúc, mọi nơi',
            'background_image' => 'https://placehold.co/1920x600/e8f0f8/cccccc?text=GiaoHang+ToanQuoc',
            'order' => 4,
            'status' => 'active',
            'user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

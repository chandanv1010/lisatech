<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HomepageSeeder extends Seeder
{
    private int $languageId = 1;
    private int $userId = 1;
    private array $widgets = [];

    public function run(): void
    {
        $this->userId = (int) (DB::table('users')->value('id') ?? 1);
        $now = now();

        $this->seedHeroBanner($now);
        $this->seedCustomerTypes($now);
        $this->seedProductCategories($now);
        $this->seedSolutions($now);
        $this->seedFeaturedProducts($now);
        $this->seedServices($now);
        $this->seedWhyLisatech($now);
        $this->seedNews($now);
    }

    private function seedHeroBanner($now): void
    {
        // Hero Menu Sidebar - "Tìm nhanh danh mục"
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

        $menuItems = [
            ['name' => 'Thiết bị, linh kiện thang máy', 'icon' => 'fa fa-building', 'canonical' => '#'],
            ['name' => 'Thiết bị nguồn', 'icon' => 'fa fa-plug', 'canonical' => '#'],
            ['name' => 'Biến tần & Servo Yaskawa - Japan', 'icon' => 'fa fa-microchip', 'canonical' => '#'],
            ['name' => 'Bộ biến đổi tần số 50Hz/60Hz/...', 'icon' => 'fa fa-bolt', 'canonical' => '#'],
            ['name' => 'Bộ lưu điện UPS Riello / Delta', 'icon' => 'fa fa-battery-half', 'canonical' => '#'],
            ['name' => 'Bộ lưu điện cửa cuốn', 'icon' => 'fa fa-columns', 'canonical' => '#'],
            ['name' => 'Ắc quy chính hãng', 'icon' => 'fa fa-car-battery', 'canonical' => '#'],
        ];

        DB::table('menus')->where('menu_catalogue_id', $catalogueId)->delete();
        foreach ($menuItems as $index => $item) {
            $menuId = DB::table('menus')->insertGetId([
                'parent_id' => 0,
                'menu_catalogue_id' => $catalogueId,
                'lft' => 0, 'rgt' => 0, 'level' => 0,
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

        // Hero Content Widget
        $mainHeroPostId = $this->upsertPost(
            'Giải pháp điều khiển & thiết bị công nghiệp cho bán lẻ, phân phối và dự án',
            'Lisatech - nơi những tài năng Việt làm việc cùng nhau trong một môi trường lành mạnh, không ngừng sáng tạo và cải tiến, một môi trường đủ thử thách để mang tới những sản phẩm và dịch vụ vươn ra toàn cầu.',
            'https://placehold.co/1920x600/e8f0f8/cccccc?text=Thay+Anh+Nen+Thanh+Pho+Vao+Day',
            1
        );
        DB::table('posts')->where('id', $mainHeroPostId)->update([
            'album' => json_encode([
                ['label' => 'Tìm sản phẩm', 'url' => '#', 'icon' => 'fa fa-shopping-cart'],
                ['label' => 'Yêu cầu báo giá', 'url' => '#', 'icon' => 'fa fa-paper-plane']
            ], JSON_UNESCAPED_UNICODE)
        ]);

        $feat1Id = $this->upsertPost('Hàng chính hãng<br>CO/CQ đầy đủ', 'Hàng chính hãng CO/CQ đầy đủ', '', 2);
        DB::table('posts')->where('id', $feat1Id)->update(['icon' => 'fa fa-check-circle']);

        $feat2Id = $this->upsertPost('Tư vấn kỹ thuật<br>miễn phí', 'Tư vấn kỹ thuật miễn phí', '', 3);
        DB::table('posts')->where('id', $feat2Id)->update(['icon' => 'fa fa-cog']);

        $feat3Id = $this->upsertPost('Giao hàng toàn quốc<br>nhanh chóng', 'Giao hàng toàn quốc nhanh chóng', '', 4);
        DB::table('posts')->where('id', $feat3Id)->update(['icon' => 'fa fa-truck']);

        $this->upsertWidget('hero-content', 'Nội dung Hero Banner', 'Post',
            [$mainHeroPostId, $feat1Id, $feat2Id, $feat3Id],
            'Hero banner main content and features',
            ['https://placehold.co/400x350/transparent/333333?text=Anh+San+Pham']
        );

        // Hero Slides - Only insert if table exists
        if (Schema::hasTable('hero_slides')) {
            DB::table('hero_slides')->delete();
            $slides = [
                ['title' => 'Giải pháp điều khiển & thiết bị công nghiệp', 'description' => 'cho bán lẻ, phân phối và dự án', 'subtitle' => 'cho bán lẻ, phân phối và dự án', 'background_image' => 'https://placehold.co/1920x600/e8f0f8/cccccc?text=Thay+Anh+Nen+Thanh+Pho+Vao+Day'],
                ['title' => 'Sản phẩm chính hãng', 'description' => 'CO/CQ đầy đủ - Hàng chính hãng', 'subtitle' => 'Đảm bảo chất lượng và nguồn gốc', 'background_image' => 'https://placehold.co/1920x600/e8f0f8/cccccc?text=San+Pham+Chinh+Hang'],
                ['title' => 'Tư vấn kỹ thuật', 'description' => 'miễn phí từ chuyên gia', 'subtitle' => 'Hỗ trợ khách hàng 24/7', 'background_image' => 'https://placehold.co/1920x600/e8f0f8/cccccc?text=Tuvan+Ky+Thuat'],
                ['title' => 'Giao hàng toàn quốc', 'description' => 'nhanh chóng và an toàn', 'subtitle' => 'Giao hàng mọi lúc, mọi nơi', 'background_image' => 'https://placehold.co/1920x600/e8f0f8/cccccc?text=GiaoHang+ToanQuoc'],
            ];
            foreach ($slides as $index => $slide) {
                DB::table('hero_slides')->insert([
                    'title' => $slide['title'],
                    'description' => $slide['description'],
                    'subtitle' => $slide['subtitle'],
                    'background_image' => $slide['background_image'],
                    'order' => $index + 1,
                    'status' => 'active',
                    'user_id' => $this->userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function seedCustomerTypes($now): void
    {
        $posts = [
            [
                'name' => 'Mua lẻ',
                'description' => 'Sản phẩm chính hãng, giá tốt, giao hàng nhanh.',
                'icon' => 'fa fa-shopping-cart',
                'link' => '#',
                'order' => 1
            ],
            [
                'name' => 'Nhà phân phối',
                'description' => 'Chính sách ưu đãi, hỗ trợ kỹ thuật & marketing.',
                'icon' => 'fa fa-users',
                'link' => '#',
                'order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Dự án',
                'description' => 'Giải pháp tối ưu, triển khai đồng bộ, đúng tiến độ.',
                'icon' => 'fa fa-diamond',
                'link' => '#',
                'order' => 3
            ],
        ];

        $postIds = [];
        foreach ($posts as $post) {
            $postId = $this->upsertPost($post['name'], $post['description'], '', $post['order']);
            DB::table('posts')->where('id', $postId)->update([
                'icon' => $post['icon'],
                'album' => json_encode(['link' => $post['link']], JSON_UNESCAPED_UNICODE)
            ]);
            $postIds[] = $postId;
        }

        $this->upsertWidget('customer-types', 'Loại khách hàng', 'Post', $postIds, 'Customer type selection: Mua lẻ, Nhà phân phối, Dự án');
    }

    private function seedProductCategories($now): void
    {
        $categories = [
            ['name' => 'THIẾT BỊ, LINH KIỆN THANG MÁY', 'description' => 'Sự ra đời của thang máy đã giúp cho việc di chuyển trở nên dễ dàng hơn. Chúng tôi cung cấp đầy đủ các linh kiện chất lượng cao.', 'icon' => 'images/icon1.png', 'image' => 'images/product1.png'],
            ['name' => 'THIẾT BỊ NGUỒN ĐIỆN', 'description' => 'Nguồn điện ổn định là nền tảng cho mọi hệ thống công nghiệp. Sản phẩm chính hãng, bảo hành dài hạn.', 'icon' => 'images/icon2.png', 'image' => 'images/product2.png'],
            ['name' => 'BIẾN TẦN & SERVO YASKAWA', 'description' => 'Đại lý phân phối ủy quyền Yaskawa Nhật Bản. Sản phẩm chất lượng cao, giá cạnh tranh.', 'icon' => 'images/icon3.png', 'image' => 'images/product3.png'],
            ['name' => 'BỘ BIẾN ĐỔI TẦN SỐ 50HZ/60HZ...', 'description' => 'Giải pháp chuyển đổi tần số cho các ứng dụng công nghiệp, âm thanh, và hàng hải.', 'icon' => 'images/icon4.png', 'image' => 'images/product4.png'],
            ['name' => 'BỘ LƯU ĐIỆN UPS RIELLO / DELTA', 'description' => 'UPS chất lượng cao từ Riello (Italia) và Delta (Đài Loan). Bảo vệ thiết bị an toàn.', 'icon' => 'images/icon5.png', 'image' => 'images/product5.png'],
            ['name' => 'ẮC QUY CHÍNH HÃNG', 'description' => 'Ắc quy các loại: Axit chì, Gel, AGM từ các hãng nổi tiếng như Ritar, CSB, Panasonic.', 'icon' => 'images/icon6.png', 'image' => 'images/product6.png'],
        ];

        $postIds = [];
        foreach ($categories as $index => $cat) {
            $postId = $this->upsertPost($cat['name'], $cat['description'], '', $index + 1);
            DB::table('posts')->where('id', $postId)->update([
                'album' => json_encode(['icon' => $cat['icon'], 'product' => $cat['image']], JSON_UNESCAPED_UNICODE)
            ]);
            $postIds[] = $postId;
        }

        $this->upsertWidget('product-categories', 'Danh Mục Sản Phẩm', 'Post', $postIds, 'Product categories for homepage');
    }

    private function seedSolutions($now): void
    {
        $solutions = [
            ['name' => 'Công nghiệp', 'icon' => 'path/to/industry-icon.svg'],
            ['name' => 'Y tế - Bệnh viện', 'icon' => 'path/to/hospital-icon.svg'],
            ['name' => 'Vận tải', 'icon' => 'path/to/transport-icon.svg'],
            ['name' => 'Sân bay', 'icon' => 'path/to/airport-icon.svg'],
            ['name' => 'Trung tâm dữ liệu', 'icon' => 'path/to/datacenter-icon.svg'],
            ['name' => 'Đóng tàu', 'icon' => 'path/to/ship-icon.svg'],
            ['name' => 'Thang máy', 'icon' => 'path/to/elevator-icon.svg'],
            ['name' => 'Tòa nhà', 'icon' => 'path/to/building-icon.svg'],
        ];

        $postIds = [];
        foreach ($solutions as $index => $sol) {
            $postId = $this->upsertPost($sol['name'], '', '', $index + 1);
            DB::table('posts')->where('id', $postId)->update(['icon' => $sol['icon']]);
            $postIds[] = $postId;
        }

        $this->upsertWidget('solutions', 'Giải Pháp & Lĩnh Vực', 'Post', $postIds, 'Solutions and industries served');
    }

    private function seedFeaturedProducts($now): void
    {
        $products = [
            ['name' => 'Bộ nghịch lưu Inverter 110VDC/220VDC sang 220VAC', 'description' => 'Điều khiển thang - Ổn định - An toàn', 'image' => 'path/to/product.png'],
            ['name' => 'Biến tần Yaskawa GA500 5HP 3Phase', 'description' => 'Biến tần đa năng cho bơm, quạt, máy nén', 'image' => 'path/to/product2.png'],
            ['name' => 'Bộ lưu điện UPS Riello 10KVA', 'description' => 'Online double conversion, thời gian lưu điện dài', 'image' => 'path/to/product3.png'],
        ];

        $postIds = [];
        foreach ($products as $index => $prod) {
            $postId = $this->upsertPost($prod['name'], $prod['description'], $prod['image'], $index + 1);
            $postIds[] = $postId;
        }

        $this->upsertWidget('featured-products', 'Sản Phẩm Nổi Bật', 'Post', $postIds, 'Featured products on homepage');
    }

    private function seedServices($now): void
    {
        $services = [
            ['name' => 'Tư vấn biến tần', 'icon' => 'fa fa-line-chart'],
            ['name' => 'Tư vấn thiết bị nguồn', 'icon' => 'fa fa-cogs'],
            ['name' => 'Tư vấn UPS', 'icon' => 'fa fa-battery-full'],
            ['name' => 'Tư vấn - điện tử - tự động hóa', 'icon' => 'fa fa-microchip'],
            ['name' => 'Giải pháp PV-Wind-Diesel', 'icon' => 'fa fa-sun-o'],
        ];

        $postIds = [];
        foreach ($services as $index => $svc) {
            $postId = $this->upsertPost($svc['name'], '', '', $index + 1);
            DB::table('posts')->where('id', $postId)->update(['icon' => $svc['icon']]);
            $postIds[] = $postId;
        }

        $this->upsertWidget('services', 'Dịch Vụ & Tư Vấn', 'Post', $postIds, 'Consulting services offered');
    }

    private function seedWhyLisatech($now): void
    {
        $reasons = [
            ['name' => 'Nhà phân phối chính thức', 'icon' => 'fa fa-university'],
            ['name' => 'Hỗ trợ kỹ thuật 24/7', 'icon' => 'fa fa-clock-o'],
            ['name' => 'Giải pháp tối ưu cho dự án', 'icon' => 'fa fa-lightbulb-o'],
            ['name' => 'Mạng lưới phân phối & xuất khẩu', 'icon' => 'fa fa-globe'],
            ['name' => 'Đội ngũ kỹ sư giàu kinh nghiệm', 'icon' => 'fa fa-users'],
            ['name' => 'Bảo hành chính hãng, bảo trì nhanh chóng', 'icon' => 'fa fa-shield'],
        ];

        $postIds = [];
        foreach ($reasons as $index => $reason) {
            $postId = $this->upsertPost($reason['name'], '', '', $index + 1);
            DB::table('posts')->where('id', $postId)->update(['icon' => $reason['icon']]);
            $postIds[] = $postId;
        }

        $this->upsertWidget('why-lisatech', 'Vì Sao Chọn Lisatech?', 'Post', $postIds, 'Why choose Lisatech');
    }

    private function seedNews($now): void
    {
        $news = [
            [
                'name' => 'LiSA20 được nhiều chuyên gia tại Interlift đánh giá cao, và nó là một bước tiến đột phá',
                'description' => 'Tin tức Logistics',
                'image' => 'https://picsum.photos/600/380?1'
            ],
            [
                'name' => 'Lựa chọn bộ cứu hộ thang máy thế nào cho phù hợp với nhu cầu của người sử dụng',
                'description' => 'Tin công nghệ',
                'image' => 'https://picsum.photos/600/380?2'
            ],
            [
                'name' => 'Tuyển dụng nhân viên kinh doanh - tháng 06/2026',
                'description' => 'Tin tuyển dụng',
                'image' => 'https://picsum.photos/600/380?3'
            ],
            [
                'name' => 'LiSA20 được nhiều chuyên gia tại Interlift đánh giá cao, và nó là một bước tiến đột phá',
                'description' => 'Tin tức khác',
                'image' => 'https://picsum.photos/600/380?4'
            ],
        ];

        $postIds = [];
        foreach ($news as $index => $item) {
            $postId = $this->upsertPost($item['name'], $item['description'], $item['image'], $index + 1);
            $postIds[] = $postId;
        }

        $this->upsertWidget('news', 'Tin Tức & Insights', 'Post', $postIds, 'News and insights section');
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

    private function upsertWidget(string $keyword, string $name, string $model, array $modelIds, string $description, array $album = []): void
    {
        $now = now();
        DB::table('widgets')->updateOrInsert(
            ['keyword' => $keyword],
            [
                'name' => $name,
                'description' => json_encode([$this->languageId => $description], JSON_UNESCAPED_UNICODE),
                'album' => json_encode($album, JSON_UNESCAPED_UNICODE),
                'model_id' => json_encode(array_values($modelIds)),
                'model' => $model,
                'short_code' => '',
                'publish' => 2,
                'note' => 'Homepage dynamic widget',
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}

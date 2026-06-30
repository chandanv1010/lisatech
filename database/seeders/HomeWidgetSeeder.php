<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * LisatechHomeWidgetSeeder
 * 
 * Seeder tạo widgets cho trang chủ lisatech.vn
 * Sử dụng đúng ID từ data thực tế trong DB:
 *   - product_catalogues: 74 (Sản phẩm gốc), 82, 83, 104, 1157...
 *   - products: 186, 187, 188, 217, 194, 226...
 *   - post_catalogues: 56 (Tin tức gốc), 57, 58, 59...
 */
class HomeWidgetSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        /**
         * ============================================================
         * BƯỚC 1: Kiểm tra dữ liệu tham chiếu trước khi seed
         * ============================================================
         */
        $this->verifyReferenceData();

        /**
         * ============================================================
         * BƯỚC 2: Định nghĩa danh sách widgets
         * ============================================================
         * Mỗi widget tương ứng với 1 section trên trang chủ theo design:
         * 1. hero-content    → Section banner hero (title + desc + buttons + features)
         * 2. hero-center     → Ảnh sản phẩm trung tâm ở hero
         * 3. hero-sidebar    → Card danh mục nhanh bên phải hero
         * 4. customer-types  → 3 loại khách hàng (bán lẻ, phân phối, dự án)
         * 5. product-categories → Grid 6 danh mục sản phẩm
         * 6. solutions       → Grid giải pháp & lĩnh vực
         * 7. featured-products → Carousel sản phẩm nổi bật
         * 8. services        → Grid 6 dịch vụ & tư vấn
         * 9. why-lisatech    → Lý do chọn Lisatech
         * 10. news           → Tin tức & Insights
         */
        $widgets = [

            // ============================================================
            // WIDGET 1: hero-content
            // Hiển thị: Tiêu đề hero, mô tả, 2 nút CTA, 3 tính năng nổi bật
            // Nguồn: model ProductCatalogue ID 74 (Sản phẩm)
            // album: JSON array chứa các button CTA
            // ============================================================
            [
                'name'       => 'Giải pháp điều khiển & Thiết bị công nghiệp',
                'keyword'    => 'hero-content',
                'short_code' => '',
                // description chứa map ngôn ngữ → mô tả hero
                'description' => json_encode([
                    1 => 'Lisatech – Nhà phân phối độc quyền thiết bị điều khiển thang máy LiSA-Schneider, biến tần Yaskawa, bộ lưu điện UPS Riello và ắc quy viễn thông. Phục vụ bán lẻ, phân phối và dự án.',
                ]),
                'model'      => 'ProductCatalogue',
                // model_id: ID 74 = "Sản phẩm" (root catalogue có đủ sub-categories)
                'model_id'   => json_encode([74]),
                // album: buttons CTA
                'album'      => json_encode([
                    [
                        'url'   => '/san-pham/san-pham/c74',
                        'icon'  => 'fa-th-large',
                        'label' => 'Xem Sản Phẩm',
                    ],
                    [
                        'url'   => '#contact',
                        'icon'  => 'fa-phone',
                        'label' => 'Liên Hệ Tư Vấn',
                    ],
                ]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ============================================================
            // WIDGET 2: hero-center
            // Hiển thị: Ảnh sản phẩm chính giữa hero banner
            // Nguồn: album-based (ảnh tủ điều khiển thang máy)
            // ============================================================
            [
                'name'       => 'Hình Ảnh Hero Trung Tâm',
                'keyword'    => 'hero-center',
                'short_code' => '',
                'description' => null,
                'model'      => 'ProductCatalogue',
                'model_id'   => json_encode([74]),
                // album: ảnh hero center – dùng ảnh thực từ uploads
                'album'      => json_encode([
                    [
                        'image' => '/upload/images/tu%20dien%20lisa/Control%20Cabinet%20Lisa-schneider%20machine%20room.jpg',
                    ],
                ]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ============================================================
            // WIDGET 3: hero-sidebar
            // Hiển thị: Card "Tìm nhanh danh mục" bên phải hero
            // Nguồn: album-based với canonical thực từ product_catalogue_language
            // Các canonical lấy từ DB: san-pham/..., ac-quy-pc1157-html, ...
            // ============================================================
            [
                'name'       => 'Tìm Nhanh Danh Mục',
                'keyword'    => 'hero-sidebar',
                'short_code' => '',
                'description' => null,
                'model'      => '',
                'model_id'   => json_encode([]),
                'album'      => json_encode([
                    [
                        'name'      => 'Thiết bị, Linh kiện Thang máy',
                        'canonical' => '/san-pham/thiet-bi-linh-kien-thang-may/c82',
                        'icon'      => 'fa-cogs',
                    ],
                    [
                        'name'      => 'Hệ điều khiển LiSA-Schneider',
                        'canonical' => '/san-pham/he-thong-dieu-khien-thang-may-lisa10/c95',
                        'icon'      => 'fa-microchip',
                    ],
                    [
                        'name'      => 'Biến tần & Servo Yaskawa',
                        'canonical' => '/san-pham/bien-tan-servo-yaskawa-japan/c104',
                        'icon'      => 'fa-bolt',
                    ],
                    [
                        'name'      => 'Bộ lưu điện UPS Riello',
                        'canonical' => '/san-pham/bo-luu-dien-riello-ups-italy/c83',
                        'icon'      => 'fa-battery-full',
                    ],
                    [
                        'name'      => 'Ắc quy viễn thông',
                        'canonical' => '/ac-quy-pc1157-html',
                        'icon'      => 'fa-car-battery',
                    ],
                    [
                        'name'      => 'Thiết bị nguồn điện',
                        'canonical' => '/thiet-bi-nguon',
                        'icon'      => 'fa-plug',
                    ],
                ]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ============================================================
            // WIDGET 4: customer-types
            // Hiển thị: 3 cột "Mua lẻ / Nhà phân phối / Dự án" dưới hero
            // Nguồn: album-based (không dùng model)
            // Theo design: icon + tên + mô tả + link
            // ============================================================
            [
                'name'       => 'Đối Tượng Khách Hàng',
                'keyword'    => 'customer-types',
                'short_code' => '',
                'description' => null,
                'model'      => '',
                'model_id'   => json_encode([]),
                'album'      => json_encode([
                    [
                        'link'        => '/san-pham/san-pham/c74',
                        'icon'        => 'fa-shopping-cart',
                        'name'        => 'Mua Lẻ',
                        'description' => "Mua sản phẩm đơn lẻ\nGiao hàng toàn quốc\nBảo hành chính hãng",
                    ],
                    [
                        'link'        => '#contact',
                        'icon'        => 'fa-handshake',
                        'name'        => 'Nhà Phân Phối',
                        'description' => "Giá đại lý cạnh tranh\nHỗ trợ kỹ thuật\nChính sách ưu đãi",
                    ],
                    [
                        'link'        => '#contact',
                        'icon'        => 'fa-building',
                        'name'        => 'Dự Án',
                        'description' => "Tư vấn giải pháp trọn gói\nKhảo sát & thiết kế\nLắp đặt & nghiệm thu",
                    ],
                ]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ============================================================
            // WIDGET 5: product-categories
            // Hiển thị: Grid 6 danh mục sản phẩm chính
            // Nguồn: model ProductCatalogue – dùng các ID level-2 của catalogue 74
            //   82 = Thiết bị linh kiện thang máy
            //   83 = Bộ lưu điện UPS RIELLO
            //   104 = Biến tần & Servo Yaskawa
            //  1156 = Thiết bị nguồn
            //  1157 = Ắc quy
            //  1162 = Bộ biến đổi tần số 50/60/400Hz
            // Blade đọc: $productCat->languages->first()->name & canonical
            //            $catImages = json_decode($productCat->album, true)
            //            $icon = $catImages['icon'], $productImage = $catImages['product']
            // ============================================================
            [
                'name'       => 'Danh Mục Sản Phẩm',
                'keyword'    => 'product-categories',
                'short_code' => '',
                'description' => null,
                'model'      => 'ProductCatalogue',
                // 6 danh mục cấp 2 của root 74
                'model_id'   => json_encode([82, 83, 104, 1156, 1157, 1162]),
                // album: object chứa icon/product images mặc định
                // Blade sẽ đọc: $catImages['icon'] và $catImages['product']
                // Mỗi catalogue có thể có album riêng trong bảng product_catalogues
                'album'      => json_encode([
                    'icon'    => '/upload/images/icon-1.png',
                    'product' => '/uploads/images/slide/master-hp-riello-ups-slide.png',
                ]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ============================================================
            // WIDGET 6: solutions
            // Hiển thị: Grid giải pháp & lĩnh vực ứng dụng
            // Nguồn: album-based (5–6 lĩnh vực)
            // Lấy từ domain thực: thang máy, tàu biển, máy bay, HVAC, năng lượng mặt trời
            // ============================================================
            [
                'name'       => 'Giải Pháp & Lĩnh Vực',
                'keyword'    => 'solutions',
                'short_code' => '',
                'description' => null,
                'model'      => '',
                'model_id'   => json_encode([]),
                'album'      => json_encode([
                    [
                        'name'      => 'Thang Máy',
                        'icon'      => 'fa-arrow-up',
                        'image'     => '',
                        'canonical' => '/san-pham/thiet-bi-linh-kien-thang-may/c82',
                    ],
                    [
                        'name'      => 'Tàu Biển',
                        'icon'      => 'fa-ship',
                        'image'     => '',
                        'canonical' => '/san-pham/bo-bien-doi-tan-so-50hz-60hz-cho-dong-tau/c79',
                    ],
                    [
                        'name'      => 'Hàng Không',
                        'icon'      => 'fa-plane',
                        'image'     => '',
                        'canonical' => '/san-pham/bo-nguon-mat-dat-gpu-cho-may-bay/c101',
                    ],
                    [
                        'name'      => 'HVAC & Công Nghiệp',
                        'icon'      => 'fa-industry',
                        'image'     => '',
                        'canonical' => '/san-pham/bien-tan-yaskawa-havc-z1000/c124',
                    ],
                    [
                        'name'      => 'Năng Lượng Mặt Trời',
                        'icon'      => 'fa-sun',
                        'image'     => '',
                        'canonical' => '/ac-quy-nang-luong-mat-troi',
                    ],
                    [
                        'name'      => 'Viễn Thông & UPS',
                        'icon'      => 'fa-broadcast-tower',
                        'image'     => '',
                        'canonical' => '/san-pham/bo-luu-dien-riello-ups-italy/c83',
                    ],
                    [
                        'name'      => 'Đường Sắt',
                        'icon'      => 'fa-train',
                        'image'     => '',
                        'canonical' => '/san-pham/bo-nguon-chuyen-biet-ac-dc-dc-dc-dc-ac-ac-ac-dung-cho-cong-nghiep-duong-sat/c110',
                    ],
                    [
                        'name'      => 'Hệ Thống Điện',
                        'icon'      => 'fa-bolt',
                        'image'     => '',
                        'canonical' => '/thiet-bi-nguon',
                    ],
                ]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ============================================================
            // WIDGET 7: featured-products
            // Hiển thị: Carousel sản phẩm nổi bật (6 sản phẩm)
            // Nguồn: model Product – dùng ID thực tế từ DB
            //   186 = Lisa 10 (HĐK thang máy LiSA-Schneider)
            //   187 = Bộ cứu hộ thang máy SOJI FJ620N
            //   217 = Biến tần Yaskawa L1000A
            //   191 = Bộ lưu điện Riello UPS
            //   216 = Ắc quy Ritar 12V-100Ah
            //   226 = Bộ cứu hộ 60VDC
            // ============================================================
            [
                'name'       => 'Sản Phẩm Nổi Bật',
                'keyword'    => 'featured-products',
                'short_code' => '',
                'description' => null,
                'model'      => 'Product',
                'model_id'   => json_encode([186, 187, 217, 191, 216, 226]),
                'album'      => json_encode([]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ============================================================
            // WIDGET 8: services
            // Hiển thị: Grid 6 dịch vụ & tư vấn
            // Nguồn: album-based (dịch vụ thực của Lisatech)
            // ============================================================
            [
                'name'       => 'Dịch Vụ & Tư Vấn',
                'keyword'    => 'services',
                'short_code' => '',
                'description' => null,
                'model'      => '',
                'model_id'   => json_encode([]),
                'album'      => json_encode([
                    [
                        'icon' => 'fa-tools',
                        'name' => "Lắp đặt & bảo trì\nThiết bị thang máy",
                    ],
                    [
                        'icon' => 'fa-comments',
                        'name' => "Tư vấn giải pháp\nKỹ thuật điện",
                    ],
                    [
                        'icon' => 'fa-battery-full',
                        'name' => "Kiểm tra & đánh giá\nẮc quy viễn thông",
                    ],
                    [
                        'icon' => 'fa-cogs',
                        'name' => "Bảo trì định kỳ\nBộ lưu điện UPS",
                    ],
                    [
                        'icon' => 'fa-truck',
                        'name' => "Nhập khẩu & phân phối\nThiết bị chính hãng",
                    ],
                    [
                        'icon' => 'fa-headset',
                        'name' => "Hỗ trợ kỹ thuật\n24/7 toàn quốc",
                    ],
                ]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ============================================================
            // WIDGET 9: why-lisatech
            // Hiển thị: 4–5 lý do chọn Lisatech
            // Nguồn: album-based
            // ============================================================
            [
                'name'       => 'Vì Sao Chọn Lisatech?',
                'keyword'    => 'why-lisatech',
                'short_code' => '',
                'description' => null,
                'model'      => '',
                'model_id'   => json_encode([]),
                'album'      => json_encode([
                    [
                        'icon' => 'fa-award',
                        'name' => "Đại lý độc quyền\nSchneider & Yaskawa tại Việt Nam",
                    ],
                    [
                        'icon' => 'fa-history',
                        'name' => "Hơn 15 năm\nkinh nghiệm trong ngành",
                    ],
                    [
                        'icon' => 'fa-certificate',
                        'name' => "Sản phẩm chính hãng\n100% có CO/CQ đầy đủ",
                    ],
                    [
                        'icon' => 'fa-headset',
                        'name' => "Đội ngũ kỹ sư\nchuyên nghiệp, tận tâm",
                    ],
                    [
                        'icon' => 'fa-truck',
                        'name' => "Giao hàng toàn quốc\nbảo hành dài hạn",
                    ],
                ]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ============================================================
            // WIDGET 10: news
            // Hiển thị: 3–4 bài viết tin tức mới nhất
            // Nguồn: model PostCatalogue – ID 56 (root) hoặc 57/58 (sub-catalogue)
            //   56 = Root "Tin tức & Thông tin" (parentid = 0, level = 1)
            //   57 = Sub-catalogue level 2 (Tin tức chung)
            //   58 = Sub-catalogue level 2 (có ảnh banner)
            // Blade đọc: $widgets['news']->object → posts, $nLang->name, $nLang->canonical
            // ============================================================
            [
                'name'       => 'Tin Tức & Insights',
                'keyword'    => 'news',
                'short_code' => '',
                'description' => null,
                'model'      => 'PostCatalogue',
                // ID 56 = root catalogue tin tức, sẽ load bài viết từ tất cả con
                'model_id'   => json_encode([56]),
                'album'      => json_encode([]),
                'publish'    => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        /**
         * ============================================================
         * BƯỚC 3: Insert hoặc Update từng widget
         * ============================================================
         * Logic: check theo keyword, nếu đã tồn tại thì UPDATE, chưa có thì INSERT
         */
        foreach ($widgets as $widgetData) {
            $this->upsertWidget($widgetData);
        }

        $this->command->info('');
        $this->command->info('✅ LisatechHomeWidgetSeeder hoàn thành!');
        $this->command->info('   Đã seed ' . count($widgets) . ' widgets cho trang chủ.');
        $this->command->info('');
        $this->command->info('📋 Danh sách widgets đã seed:');
        foreach ($widgets as $w) {
            $this->command->line("   [{$w['keyword']}] → {$w['name']}");
        }
    }

    /**
     * Insert hoặc Update widget theo keyword
     * Nếu widget đã tồn tại (chưa bị soft delete) → UPDATE
     * Nếu chưa tồn tại → INSERT
     */
    private function upsertWidget(array $widgetData): void
    {
        // Tìm widget đang active (không bị soft delete)
        $existingId = DB::table('widgets')
            ->where('keyword', $widgetData['keyword'])
            ->whereNull('deleted_at')
            ->value('id');

        if ($existingId) {
            // Cập nhật widget hiện có (không thay đổi created_at)
            $updateData = $widgetData;
            unset($updateData['created_at']);

            DB::table('widgets')
                ->where('id', $existingId)
                ->update($updateData);

            $this->command->line("   🔄 Updated [{$widgetData['keyword']}]");
        } else {
            // Tạo mới widget
            DB::table('widgets')->insert($widgetData);
            $this->command->line("   ➕ Created [{$widgetData['keyword']}]");
        }
    }

    /**
     * Kiểm tra dữ liệu tham chiếu có tồn tại trong DB hay không
     * Cảnh báo nếu thiếu, nhưng không dừng seeder
     */
    private function verifyReferenceData(): void
    {
        $this->command->info('🔍 Đang kiểm tra dữ liệu tham chiếu...');

        // Kiểm tra ProductCatalogues
        $catalogueIds = [74, 82, 83, 104, 1156, 1157, 1162];
        foreach ($catalogueIds as $id) {
            $exists = DB::table('product_catalogues')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->exists();

            if (!$exists) {
                $this->command->warn("   ⚠️  product_catalogues ID={$id} không tồn tại!");
            }
        }

        // Kiểm tra Products
        $productIds = [186, 187, 217, 191, 216, 226];
        foreach ($productIds as $id) {
            $exists = DB::table('products')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->exists();

            if (!$exists) {
                $this->command->warn("   ⚠️  products ID={$id} không tồn tại!");
            }
        }

        // Kiểm tra PostCatalogues
        $postCatalogueIds = [56];
        foreach ($postCatalogueIds as $id) {
            $exists = DB::table('post_catalogues')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->exists();

            if (!$exists) {
                $this->command->warn("   ⚠️  post_catalogues ID={$id} không tồn tại!");
            }
        }

        $this->command->info('   ✅ Kiểm tra xong.');
        $this->command->info('');
    }
}

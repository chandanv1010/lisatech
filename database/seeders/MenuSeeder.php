<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $languageId = (int) (DB::table('languages')->where('canonical', 'vi')->value('id') ?? 0);
        $adminUserId = (int) (DB::table('users')->where('email', 'admin@gmail.com')->value('id') ?? 1);

        if (!$languageId) {
            $languageId = DB::table('languages')->insertGetId([
                'name' => 'Tiếng Việt',
                'canonical' => 'vi',
                'image' => '',
                'user_id' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $catalogueId = DB::table('menu_catalogues')->where('keyword', 'main-menu')->value('id');
        if (!$catalogueId) {
            $catalogueId = DB::table('menu_catalogues')->insertGetId([
                'name' => 'Main menu',
                'keyword' => 'main-menu',
                'publish' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $menuItems = [
            ['name' => 'Trang chủ', 'canonical' => '/'],
            ['name' => 'Giới thiệu', 'canonical' => '/gioi-thieu'],
            ['name' => 'Dịch vụ', 'canonical' => '/dich-vu'],
            ['name' => 'Tin tức', 'canonical' => '/tin-tuc'],
            ['name' => 'Liên hệ', 'canonical' => '/lien-he'],
        ];

        foreach ($menuItems as $index => $item) {
            $menuId = DB::table('menus')->where('menu_catalogue_id', $catalogueId)
                ->where('parent_id', 0)
                ->skip($index)
                ->value('id');

            if (!$menuId) {
                $menuId = DB::table('menus')->insertGetId([
                    'parent_id' => 0,
                    'menu_catalogue_id' => $catalogueId,
                    'lft' => 0,
                    'rgt' => 0,
                    'level' => 0,
                    'type' => 'link',
                    'publish' => 1,
                    'order' => $index + 1,
                    'user_id' => $adminUserId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('menu_language')->updateOrInsert(
                ['menu_id' => $menuId, 'language_id' => $languageId],
                [
                    'menu_id' => $menuId,
                    'language_id' => $languageId,
                    'name' => $item['name'],
                    'canonical' => $item['canonical'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}

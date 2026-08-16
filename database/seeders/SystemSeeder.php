<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $adminUserId = (int) (DB::table('users')->where('email', 'admin@gmail.com')->value('id') ?? 1);

        $languageId = (int) (DB::table('languages')->where('canonical', 'vi')->value('id') ?? 0);
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

        $systemEntries = [
            ['keyword' => 'contact_email', 'content' => 'contact@lisatech.vn'],
            ['keyword' => 'contact_hotline', 'content' => '0939971988'],
            ['keyword' => 'social_facebook', 'content' => 'https://www.facebook.com/'],
            ['keyword' => 'social_zalo', 'content' => 'https://zalo.me/'],
            ['keyword' => 'link_register', 'content' => '/dang-ky'],
            ['keyword' => 'homepage_logo', 'content' => '/images/logo.png'],
            ['keyword' => 'seo_meta_title', 'content' => 'LisaTech'],

            // Support contacts (Hỗ trợ trực tuyến)
            ['keyword' => 'support_name_1', 'content' => 'Nguyên'],
            ['keyword' => 'support_phone_1', 'content' => '0939971988'],
            ['keyword' => 'support_zalo_1', 'content' => 'https://zalo.me/0939971988'],

            ['keyword' => 'support_name_2', 'content' => 'Quân'],
            ['keyword' => 'support_phone_2', 'content' => '0944 411 023'],
            ['keyword' => 'support_zalo_2', 'content' => 'https://zalo.me/0944411023'],

            ['keyword' => 'support_name_3', 'content' => 'Hằng'],
            ['keyword' => 'support_phone_3', 'content' => '0369363224'],
            ['keyword' => 'support_zalo_3', 'content' => 'https://zalo.me/0369363224'],

            ['keyword' => 'support_name_4', 'content' => 'Vân'],
            ['keyword' => 'support_phone_4', 'content' => '0359977896'],
            ['keyword' => 'support_zalo_4', 'content' => 'https://zalo.me/0359977896'],

            ['keyword' => 'support_name_5', 'content' => ''],
            ['keyword' => 'support_phone_5', 'content' => ''],
            ['keyword' => 'support_zalo_5', 'content' => ''],
        ];

        foreach ($systemEntries as $entry) {
            DB::table('systems')->updateOrInsert(
                [
                    'keyword' => $entry['keyword'],
                    'language_id' => $languageId,
                ],
                [
                    'language_id' => $languageId,
                    'user_id' => $adminUserId,
                    'keyword' => $entry['keyword'],
                    'content' => $entry['content'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}

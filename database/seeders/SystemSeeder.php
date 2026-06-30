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
            ['keyword' => 'contact_email', 'content' => 'info@lisa.edu.vn'],
            ['keyword' => 'contact_hotline', 'content' => '0123.456.789'],
            ['keyword' => 'social_facebook', 'content' => 'https://www.facebook.com/'],
            ['keyword' => 'social_zalo', 'content' => 'https://zalo.me/'],
            ['keyword' => 'link_register', 'content' => '/dang-ky'],
            ['keyword' => 'homepage_logo', 'content' => '/images/logo.png'],
            ['keyword' => 'seo_meta_title', 'content' => 'Karaoke'],
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

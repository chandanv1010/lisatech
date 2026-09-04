<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dua thong tin "Ho tro truc tuyen" ve dung language_id ma frontend doc.
 *
 * Bang `languages` co hai ban ghi tieng Viet trung nhau: id=1 (canonical "vn")
 * va id=5 (canonical "vi"). Hai tang doc/ghi lai lay language khac nhau:
 *
 *   - Frontend: config('app.language_id', 1)  -> luon la 1
 *   - Admin   : app()->getLocale() -> tra canonical -> co the ra 1 hoac 5
 *
 * Nen thong tin ho tro tung duoc nhap khi locale la "vi" da luu vao id=5, con
 * id=1 chi con placeholder ("Ho tro 1" / "0973 999 999"). Blade nhan ra do la
 * placeholder va roi ve gia tri mac dinh hard code, khien admin sua bao nhieu
 * cung khong an.
 *
 * Seeder nay chep gia tri that sang language_id=1. Chay lai nhieu lan van an
 * toan: chi ghi de khi o dich dang rong hoac dang la placeholder.
 */
class SupportContactLanguageSeeder extends Seeder
{
    /** language_id ma frontend doc. */
    private const TARGET_LANGUAGE = 1;

    /** language_id dang giu du lieu that. */
    private const SOURCE_LANGUAGE = 5;

    /**
     * Gia tri du phong, dung khi ban ghi o SOURCE_LANGUAGE khong con.
     * Day chinh la 4 dong dang bi hard code trong blade.
     */
    private const FALLBACK = [
        'support_name_1'  => 'Nguyên',
        'support_phone_1' => '0939971988',
        'support_zalo_1'  => 'https://zalo.me/0939971988',
        'support_name_2'  => 'Quân',
        'support_phone_2' => '0944 411 023',
        'support_zalo_2'  => 'https://zalo.me/0944411023',
        'support_name_3'  => 'Hằng',
        'support_phone_3' => '0369363224',
        'support_zalo_3'  => 'https://zalo.me/0369363224',
        'support_name_4'  => 'Vân',
        'support_phone_4' => '0359977896',
        'support_zalo_4'  => 'https://zalo.me/0359977896',
    ];

    public function run(): void
    {
        $userId = DB::table('users')->whereNull('deleted_at')->min('id')
            ?? DB::table('users')->min('id')
            ?? 1;

        $source = DB::table('systems')
            ->where('language_id', self::SOURCE_LANGUAGE)
            ->where('keyword', 'LIKE', 'support_%')
            ->pluck('content', 'keyword')
            ->filter(fn ($v) => trim((string) $v) !== '')
            ->all();

        $values = $source ?: self::FALLBACK;
        $written = $skipped = 0;

        foreach ($values as $keyword => $content) {
            if (trim((string) $content) === '') {
                continue;
            }

            $current = DB::table('systems')
                ->where('keyword', $keyword)
                ->where('language_id', self::TARGET_LANGUAGE)
                ->value('content');

            // Khong dam vao gia tri admin da nhap that.
            if (!$this->isPlaceholder($current)) {
                $skipped++;
                continue;
            }

            if ($current === null && !DB::table('systems')
                    ->where('keyword', $keyword)
                    ->where('language_id', self::TARGET_LANGUAGE)
                    ->exists()) {
                DB::table('systems')->insert([
                    'keyword' => $keyword,
                    'content' => $content,
                    'language_id' => self::TARGET_LANGUAGE,
                    'user_id' => $userId,
                ]);
            } else {
                DB::table('systems')
                    ->where('keyword', $keyword)
                    ->where('language_id', self::TARGET_LANGUAGE)
                    ->update(['content' => $content]);
            }

            $written++;
        }

        $this->command?->info("Support contacts: ghi {$written} key, bo qua {$skipped} key da co du lieu that.");
    }

    /**
     * Cung bo dau hieu placeholder ma blade dang dung de nhan dien.
     */
    private function isPlaceholder(?string $value): bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            return true;
        }

        if (str_contains(mb_strtolower($value), 'hỗ trợ')) {
            return true;
        }

        if (str_contains($value, '0973 999 999') || str_contains($value, '0973999999')) {
            return true;
        }

        // Link Zalo tro ve trang chu, khong dan toi so nao ca.
        return rtrim($value, '/') === 'https://zalo.me';
    }
}

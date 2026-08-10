<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm cột parent_id vào bảng post_catalogues (bảng cũ dùng cột parentid)
     * và đồng bộ dữ liệu từ parentid → parent_id.
     */
    public function up(): void
    {
        // Thêm cột parent_id nếu chưa tồn tại
        if (!Schema::hasColumn('post_catalogues', 'parent_id')) {
            Schema::table('post_catalogues', function (Blueprint $table) {
                $table->integer('parent_id')->default(0)->after('id');
            });

            // Đồng bộ dữ liệu từ cột parentid sang parent_id
            if (Schema::hasColumn('post_catalogues', 'parentid')) {
                DB::statement('UPDATE post_catalogues SET parent_id = parentid');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_catalogues', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};

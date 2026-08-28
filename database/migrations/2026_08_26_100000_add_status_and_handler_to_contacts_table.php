<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm trạng thái xử lý và người xử lý cho liên hệ.
 *
 * Quy ước giá trị theo đúng lối sẵn có của dự án (1 = chưa, 2 = rồi), và điều
 * này cũng làm việc sắp xếp trở nên tự nhiên: xếp status tăng dần là liên hệ
 * chưa xử lý tự động lên đầu, không cần biểu thức sắp xếp đặc biệt nào.
 *
 * Mặc định 1 để 124 liên hệ đang có đều thành "chưa xử lý" - đúng thực tế, vì
 * trước nay chưa từng có chỗ nào đánh dấu đã xử lý.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('message');
            $table->unsignedBigInteger('handled_by')->nullable()->after('status');

            // Cột này nằm ở đầu mệnh đề sắp xếp của trang danh sách nên phải
            // có chỉ mục, nếu không thì bảng lớn dần sẽ phải quét toàn bộ.
            $table->index(['status', 'id'], 'contacts_status_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('contacts_status_id_index');
            $table->dropColumn(['status', 'handled_by']);
        });
    }
};

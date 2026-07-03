<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('download')->nullable()->after('image');
        });

        Schema::table('product_language', function (Blueprint $table) {
            $table->longText('applications')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('download');
        });

        Schema::table('product_language', function (Blueprint $table) {
            $table->dropColumn('applications');
        });
    }
};

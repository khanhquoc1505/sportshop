<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanpham_kichco_mausac', function (Blueprint $table) {
            // Thêm cột hinh_anh (đường dẫn file) sau cột sl
            $table->string('hinh_anh')->nullable()->after('sl');
        });
    }

    public function down(): void
    {
        Schema::table('sanpham_kichco_mausac', function (Blueprint $table) {
            $table->dropColumn('hinh_anh');
        });
    }
};

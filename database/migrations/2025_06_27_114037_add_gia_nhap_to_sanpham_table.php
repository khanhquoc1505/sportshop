<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            // Thêm cột gia_nhap, kiểu DECIMAL(12,2), mặc định 0
            $table->decimal('gia_nhap', 12, 2)->default(0)->after('gia_ban');
        });
    }

    public function down(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            $table->dropColumn('gia_nhap');
        });
    }
};

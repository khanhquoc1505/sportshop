<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            // 1) Xóa foreign key constraints
            $table->dropForeign(['kichco_id']);
            $table->dropForeign(['loai_id']);
            $table->dropForeign(['bomon_id']);
            $table->dropForeign(['mausac_id']);
            $table->dropForeign(['gioitinh_id']);

            // 2) Xóa luôn các cột
            $table->dropColumn([
              'kichco_id',
              'loai_id',
              'bomon_id',
              'mausac_id',
              'gioitinh_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            // Thêm lại các cột
            $table->unsignedBigInteger('kichco_id')->nullable();
            $table->unsignedBigInteger('loai_id')->nullable();
            $table->unsignedBigInteger('bomon_id')->nullable();
            $table->unsignedBigInteger('mausac_id')->nullable();
            $table->unsignedBigInteger('gioitinh_id')->nullable();

            // Thêm lại foreign key
            $table->foreign('kichco_id')->references('id')->on('kichco')->nullOnDelete();
            $table->foreign('loai_id')->references('id')->on('loai')->nullOnDelete();
            $table->foreign('bomon_id')->references('id')->on('bomon')->nullOnDelete();
            $table->foreign('mausac_id')->references('id')->on('mausac')->nullOnDelete();
            $table->foreign('gioitinh_id')->references('id')->on('gioitinh')->nullOnDelete();
        });
    }
};

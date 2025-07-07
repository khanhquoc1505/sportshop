<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('img_san_phams', function (Blueprint $table) {
            // thêm khóa ngoại sang bảng sanpham
            $table->unsignedBigInteger('sanpham_id')->after('id');
            $table->foreign('sanpham_id')
                  ->references('id')
                  ->on('sanpham')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('img_san_phams', function (Blueprint $table) {
            // rollback: bỏ foreign key rồi drop cột
            $table->dropForeign(['sanpham_id']);
            $table->dropColumn('sanpham_id');
        });
    }
};

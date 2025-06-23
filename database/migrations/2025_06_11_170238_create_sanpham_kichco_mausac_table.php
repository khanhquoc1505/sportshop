<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanpham_kichco_mausac', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại tới sản phẩm, size, màu
            $table->foreignId('sanpham_id')
                  ->constrained('sanpham')
                  ->cascadeOnDelete();

            $table->foreignId('kichco_id')
                  ->constrained('kichco')
                  ->cascadeOnDelete();

            $table->foreignId('mausac_id')
                  ->constrained('mausac')
                  ->cascadeOnDelete();

            // Số lượng cho mỗi kết hợp product-size-color
            $table->unsignedInteger('sl')->default(0);

            $table->timestamps();

            // Nếu bạn muốn đảm bảo không có duplicate 
            $table->unique(['sanpham_id','kichco_id','mausac_id'], 
                          'uniq_sp_kc_ms');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanpham_kichco_mausac');
    }
};

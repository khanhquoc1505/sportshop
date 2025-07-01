<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donhang_chitiets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donhang_id')
                  ->constrained('donhang')      // bảng đơn hàng của bạn là 'donhang'
                  ->onDelete('cascade');
            $table->foreignId('sanpham_id')
                  ->constrained('sanpham')     // bảng sản phẩm của bạn là 'sanpham'
                  ->onDelete('cascade');
            $table->integer('soluong');
            $table->decimal('dongia', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donhang_chitiets');
    }
};

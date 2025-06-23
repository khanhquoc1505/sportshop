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
        Schema::create('comment', function (Blueprint $table) {
            $table->id();
            $table->string('ten_khach_hang');
            $table->text('noi_dung');
            $table->dateTime('thoi_gian');
            $table->string('trang_thai');
            $table->string('hinh_anh')->nullable();
            $table->foreignId('sanpham_id')
                  ->nullable()
                  ->constrained('sanpham')
                  ->cascadeOnDelete();
            $table->foreignId('nguoidung_id')
                  ->nullable()
                  ->constrained('nguoidung')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment');
    }
};


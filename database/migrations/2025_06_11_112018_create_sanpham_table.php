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
        Schema::create('sanpham', function (Blueprint $table) {
            $table->id();
            $table->string('masanpham')->unique();
            $table->string('ten');
            $table->text('mo_ta')->nullable();
            $table->decimal('gia_ban', 12, 2);
            $table->string('hinh_anh')->nullable();
            $table->string('trang_thai')->default('active');
            $table->dateTime('thoi_gian_them')->useCurrent();
            $table->foreignId('kichco_id')
                  ->nullable()
                  ->constrained('kichco')
                  ->nullOnDelete();
            $table->foreignId('loai_id')
                  ->nullable()
                  ->constrained('loai')
                  ->nullOnDelete();
            $table->foreignId('bomon_id')
                  ->nullable()
                  ->constrained('bomon')
                  ->nullOnDelete();
            $table->foreignId('mausac_id')
                  ->nullable()
                  ->constrained('mausac')
                  ->nullOnDelete();
            $table->foreignId('gioitinh_id')
                  ->nullable()
                  ->constrained('gioitinh')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanpham');
    }
};

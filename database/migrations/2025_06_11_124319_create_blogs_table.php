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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('tieude'); 
            $table->text('noidung');
            $table->string('trangthai');
            $table->string('hinhdaidien')->nullable();
            $table->dateTime('thoi_gian_them');
            $table->dateTime('thoi_gian_cap_nhat')->nullable();
            $table->foreignId('sanpham_id')
                  ->nullable()
                  ->constrained('sanpham')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};

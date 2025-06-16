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
        Schema::create('yeuthich', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sanpham_id')
                  ->constrained('sanpham')
                  ->cascadeOnDelete();
            $table->foreignId('nguoidung_id')
                  ->constrained('nguoidung')
                  ->cascadeOnDelete();
            $table->dateTime('thoi_gian_them')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeuthich');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nhapkho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoidung_id')
                ->constrained('nguoidung')
                ->cascadeOnDelete();
            $table->foreignId('sanpham_id')
                ->constrained('sanpham')
                ->cascadeOnDelete();
            $table->dateTime('ngaynhap')->useCurrent();
            $table->integer('soluongnhap');
            $table->decimal('gianhap', 12, 2);
            $table->string('ghichu')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhapkho');
    }
};
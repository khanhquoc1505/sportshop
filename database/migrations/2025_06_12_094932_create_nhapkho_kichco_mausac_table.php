<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nhapkho_kichco_mausac', function (Blueprint $table) {
            $table->id();

            // FK đến lần nhập kho chung
            $table->foreignId('nhapkho_id')
                  ->constrained('nhapkho')     // tên bảng chính của bạn
                  ->cascadeOnDelete();

            // FK đến size và màu
            $table->foreignId('kichco_id')
                  ->constrained('kichco')
                  ->cascadeOnDelete();
            $table->foreignId('mausac_id')
                  ->constrained('mausac')
                  ->cascadeOnDelete();

            // Số lượng nhập cho chính variant đó
            $table->unsignedInteger('sl')->default(0);

            $table->timestamps();

            // Không cho phép lặp lại cùng 1 bộ ba nhapkho–size–màu
            $table->unique(['nhapkho_id','kichco_id','mausac_id'], 'nhapkho_kc_ms_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhapkho_kichco_mausac');
    }
};

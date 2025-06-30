<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // dùng raw để tránh lỗi ''cod''
        DB::statement("
            ALTER TABLE `donhang`
            CHANGE `payment_method` `phuong_thuc_thanh_toan`
            ENUM('cod','vnpay') NOT NULL DEFAULT 'cod'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE `donhang`
            CHANGE `phuong_thuc_thanh_toan` `payment_method`
            ENUM('cod','vnpay') NOT NULL DEFAULT 'cod'
        ");
    }
};

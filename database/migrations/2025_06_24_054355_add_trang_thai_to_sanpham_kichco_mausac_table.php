<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('sanpham_kichco_mausac', function (Blueprint $table) {
        $table->boolean('trang_thai')->default(1); // 1 = Hiển thị, 0 = Ẩn
    });
}

public function down()
{
    Schema::table('sanpham_kichco_mausac', function (Blueprint $table) {
        $table->dropColumn('trang_thai');
    });
}
};

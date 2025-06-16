<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSanphamKichcoMausacTable extends Migration
{
    public function up()
    {
        Schema::table('sanpham_kichco_mausac', function (Blueprint $table) {
            $table->dropColumn('hinh_anh'); // loại bỏ ảnh ra khỏi bảng này
        });
    }

    public function down()
    {
        Schema::table('sanpham_kichco_mausac', function (Blueprint $table) {
            $table->string('hinh_anh')->nullable();
        });
    }
}

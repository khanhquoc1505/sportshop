<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveHinhAnhFromNhapkhoKichcoMausacTable extends Migration
{
    public function up()
    {
        Schema::table('nhapkho_kichco_mausac', function (Blueprint $table) {
            if (Schema::hasColumn('nhapkho_kichco_mausac', 'hinh_anh')) {
                $table->dropColumn('hinh_anh');
            }
        });
    }

    public function down()
    {
        Schema::table('nhapkho_kichco_mausac', function (Blueprint $table) {
            $table->string('hinh_anh')->nullable();
        });
    }
}

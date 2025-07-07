<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPasswordEncToNguoidungTable extends Migration
{
    public function up()
    {
        Schema::table('nguoidung', function (Blueprint $table) {
            // text để có thể chứa chuỗi mã hoá dài
            $table->text('password_enc')->nullable()->after('mat_khau');
        });
    }

    public function down()
    {
        Schema::table('nguoidung', function (Blueprint $table) {
            $table->dropColumn('password_enc');
        });
    }
}

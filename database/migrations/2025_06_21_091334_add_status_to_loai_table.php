<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột `status` vào bảng `loai`
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loai', function (Blueprint $table) {
            // thêm cột boolean 'status', default = 1, đặt sau cột 'loai'
            $table->boolean('status')
                  ->default(1)
                  ->after('loai');
        });
    }

    /**
     * Xóa cột `status` khi rollback
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loai', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

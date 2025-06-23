<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột `noi_dung` vào bảng `danhgia`
     *
     * @return void
     */
    public function up()
{
    Schema::table('danhgia', function (Blueprint $table) {
        if (! Schema::hasColumn('danhgia', 'noi_dung')) {
            $table->text('noi_dung')->nullable()->after('sosao');
        }
    });
}

    /**
     * Xóa cột `noi_dung` khi rollback
     *
     * @return void
     */
    public function down()
    {
        Schema::table('danhgia', function (Blueprint $table) {
            $table->dropColumn('noi_dung');
        });
    }
};

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('danhgia', function (Blueprint $table) {
            // thêm cột reply để lưu nội dung phản hồi
            $table->text('reply')
                  ->nullable()
                  ->after('noi_dung');

            // thêm cột is_replied để đánh dấu đã trả lời hay chưa
            $table->boolean('is_replied')
                  ->default(false)
                  ->after('reply');
        });
    }

    public function down()
    {
        Schema::table('danhgia', function (Blueprint $table) {
            // khi rollback thì xóa hai cột này
            $table->dropColumn(['reply', 'is_replied']);
        });
    }
};

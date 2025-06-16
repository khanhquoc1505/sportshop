<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            // Nếu trước đây có chỉnh gì liên quan đến foreign key thì drop trước
            // $table->dropForeign(['hinh_anh']); // không cần nếu chỉ là cột string

            // Xóa cột hinh_anh
            $table->dropColumn('hinh_anh');
        });
    }

    public function down(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            // Thêm lại cột hinh_anh (điều chỉnh vị trí nếu cần)
            $table->string('hinh_anh')->nullable()->after('gia_ban');
        });
    }
};

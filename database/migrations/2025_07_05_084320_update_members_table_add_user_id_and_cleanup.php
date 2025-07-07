<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // 1) thêm cột user_id, unsignedBigInteger trùng kiểu với users.id
            $table->unsignedBigInteger('user_id')->after('id')->unique();

            // 2) thêm foreign key
            $table->foreign('user_id')
                  ->references('id')->on('nguoidung')  // nếu bạn dùng bảng `nguoidung`
                  ->onDelete('cascade');

            // 3) xoá những cột bạn không cần nữa
            $table->dropColumn(['name','email']); 
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // roll-back: thêm lại name, email
            $table->string('name');
            $table->string('email')->unique();
            // xoá fk và cột user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};

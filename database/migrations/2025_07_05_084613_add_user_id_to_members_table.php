<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // 1) thêm cột nullable để không vướng constraint
            $table->unsignedBigInteger('user_id')
                  ->nullable()
                  ->after('id')
                  ->comment('ràng buộc tới bảng người dùng');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};

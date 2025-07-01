<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            $table->decimal('gia_buon', 12, 2)
                  ->default(0)
                  ->after('gia_ban');
        });
    }

    public function down(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            $table->dropColumn('gia_buon');
        });
    }
};

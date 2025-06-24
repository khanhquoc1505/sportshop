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
    Schema::table('sanpham_kichco_mausac', function (Blueprint $table) {
        if (! Schema::hasColumn('sanpham_kichco_mausac', 'sanpham_id')) {
            $table->unsignedBigInteger('sanpham_id')->after('id');
            $table->foreign('sanpham_id')
                  ->references('id')->on('sanphams')
                  ->onDelete('cascade');
        }
    });
}

public function down()
{
    Schema::table('sanpham_kichco_mausac', function (Blueprint $table) {
        $table->dropForeign(['sanpham_id']);
        $table->dropColumn('sanpham_id');
    });
}

};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('voucher_sanpham', function (Blueprint $table) {
        $table->unsignedTinyInteger('discount_percent')
              ->default(0)
              ->after('sanpham_id');
    });
}
public function down()
{
    Schema::table('voucher_sanpham', function (Blueprint $table) {
        $table->dropColumn('discount_percent');
    });
}
};

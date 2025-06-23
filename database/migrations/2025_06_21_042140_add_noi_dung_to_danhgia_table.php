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
    Schema::table('danhgia', function (Blueprint $table) {
        $table->text('noi_dung')
              ->nullable()
              ->after('sosao');
    });
}
public function down()
{
    Schema::table('danhgia', function (Blueprint $table) {
        $table->dropColumn('noi_dung');
    });
}
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToDonhangTable extends Migration
{
    public function up()
    {
        Schema::table('donhang', function (Blueprint $table) {
            $table->text('notes')
                  ->nullable()
                  ->after('shipping_method'); // hoặc sau cột nào bạn muốn
        });
    }

    public function down()
    {
        Schema::table('donhang', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
}

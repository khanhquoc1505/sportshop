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
        $table->text('reply')->nullable()->after('noi_dung');
        $table->boolean('is_replied')->default(false)->after('reply');
    });
}

public function down()
{
    Schema::table('danhgia', function (Blueprint $table) {
        $table->dropColumn(['reply', 'is_replied']);
    });
}
};

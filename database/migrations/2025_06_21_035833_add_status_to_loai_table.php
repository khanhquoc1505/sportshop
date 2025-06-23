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
  Schema::table('loai', function (Blueprint $table) {
    $table->boolean('status')->default(1)->after('loai');
  });
}
public function down()
{
  Schema::table('loai', function (Blueprint $table) {
    $table->dropColumn('status');
  });
}
};

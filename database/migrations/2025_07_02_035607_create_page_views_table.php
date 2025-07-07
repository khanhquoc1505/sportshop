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
    Schema::create('page_views', function (Blueprint $table) {
        $table->id();
        // Lưu ngày (YYYY-MM-DD)
        $table->date('visited_at')->unique();
        // Tổng lượt truy cập trong ngày
        $table->unsignedBigInteger('count')->default(0);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('page_views');
}

};

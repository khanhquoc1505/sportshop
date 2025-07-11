<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('color_images', function (Blueprint $table) {
            $table->unsignedBigInteger('kichco_id')->nullable()->after('mausac_id');
            $table->foreign('kichco_id')
                  ->references('id')->on('kichco')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('color_images', function (Blueprint $table) {
            $table->dropForeign(['kichco_id']);
            $table->dropColumn('kichco_id');
        });
    }
};

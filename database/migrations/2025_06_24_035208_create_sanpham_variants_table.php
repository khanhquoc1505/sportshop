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
    Schema::create('sanpham_variants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sanpham_id')->constrained('sanpham')->cascadeOnDelete();
        $table->string('size', 10);
        $table->string('color', 50);
        $table->unsignedInteger('quantity')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanpham_variants');
    }
};

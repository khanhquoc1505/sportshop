<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColorImagesTable extends Migration
{
    public function up()
    {
        Schema::create('color_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sanpham_id');
            $table->unsignedBigInteger('mausac_id');
            $table->string('image_path');
            $table->boolean('is_main')->default(true);
            $table->timestamps();

            $table->foreign('sanpham_id')->references('id')->on('sanpham')->onDelete('cascade');
            $table->foreign('mausac_id')->references('id')->on('mausac');
        });
    }

    public function down()
    {
        Schema::dropIfExists('color_images');
    }
}


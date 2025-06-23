<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingAndDeliveryStatusToDonhangTable extends Migration
{
    public function up()
    {
        Schema::table('donhang', function (Blueprint $table) {
            // sau cột trangthaidonhang, thêm phương thức và trạng thái giao
            $table->string('shipping_method')->default('COD')->after('trangthaidonhang');
            $table->enum('delivery_status', [
                'pending',
                'waiting_pickup',
                'shipping',
                'delivered',
                'returned',
                'canceled',
                'incomplete'
            ])->default('pending')->after('shipping_method');
        });
    }

    public function down()
    {
        Schema::table('donhang', function (Blueprint $table) {
            $table->dropColumn(['shipping_method','delivery_status']);
        });
    }
}

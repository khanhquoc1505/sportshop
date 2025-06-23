<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // map COD → standard
        DB::table('donhang')
          ->where('shipping_method', 'COD')
          ->orWhere('shipping_method', 'cod')
          ->update(['shipping_method' => 'standard']);
    }

    public function down()
    {
        // (tuỳ chọn) nếu rollback thì đổi lại 'standard' → 'COD'
        DB::table('donhang')
          ->where('shipping_method', 'standard')
          ->update(['shipping_method' => 'COD']);
    }
};

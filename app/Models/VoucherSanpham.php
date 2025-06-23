<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherSanpham extends Model
{
  protected $table = 'voucher_sanpham';
  public $timestamps = true;
  protected $fillable = ['voucher_id','sanpham_id'];
}
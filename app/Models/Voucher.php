<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SanPham;

class Voucher extends Model
{
    protected $table = 'vouchers';      // hoặc tên bảng bạn đặt
    protected $fillable = [
      'ma_voucher','loai','soluong','noi_dung',
      'ngay_bat_dau','ngay_ket_thuc',
    ];

    public function sanPhams()
    {
        return $this->belongsToMany(
            SanPham::class,
            'voucher_sanpham',
            'voucher_id',
            'sanpham_id'
        );
    }
}
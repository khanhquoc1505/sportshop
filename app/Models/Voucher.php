<?php
// app/Models/Voucher.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'voucher';
    protected $fillable = ['soluong','ngay_bat_dau','ngay_ket_thuc','sanpham_id'];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class,'sanpham_id');
    }
}

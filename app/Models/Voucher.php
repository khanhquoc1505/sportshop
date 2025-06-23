<?php
// app/Models/Voucher.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $fillable = [
        'ma_voucher',
        'loai',
        'soluong',
        'noi_dung',
        'ngay_bat_dau',
        'ngay_ket_thuc',
    ];
    protected $casts = [
        'ngay_bat_dau'  => 'datetime',
        'ngay_ket_thuc' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function sanphams()
    {
        return $this->belongsToMany(SanPham::class, 'voucher_sanpham', 'voucher_id', 'sanpham_id');
    }
    
}

<?php
// app/Models/NhapKho.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhapKho extends Model
{
    protected $table = 'nhapkho';
    protected $fillable = [
        'nguoidung_id','sanpham_id','ngaynhap',
        'soluongnhap','gianhap','ghichu'
    ];

    public function user()
    {
        return $this->belongsTo(NguoiDung::class,'nguoidung_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class,'sanpham_id');
    }

    public function details()
    {
        return $this->hasMany(nhapkho_kichco_mausac::class,'nhapkho_id');
    }
}

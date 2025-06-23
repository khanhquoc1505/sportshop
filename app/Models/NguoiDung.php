<?php
// app/Models/NguoiDung.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class NguoiDung extends Authenticatable
{
    protected $table = 'nguoidung';
    protected $fillable = [
        'ten_nguoi_dung','mat_khau','email','sdt','dia_chi','vai_tro'
    ];

    public function nhapKho()
    {
        return $this->hasMany(NhapKho::class, 'nguoidung_id');
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'nguoidung_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'nguoidung_id');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'nguoidung_id');
    }

    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'nguoidung_id');
    }
    public function getAuthPassword()
{
    return $this->mat_khau;
}
}

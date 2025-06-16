<?php
// app/Models/GioiTinhSanPham.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioiTinhSanPham extends Model
{
    protected $table = 'gioitinh_sanpham';
    protected $fillable = ['sanpham_id', 'gioitinh_id'];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }

    public function gioiTinh()
    {
        return $this->belongsTo(GioiTinh::class, 'gioitinh_id');
    }
}

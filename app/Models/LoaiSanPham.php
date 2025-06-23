<?php
// app/Models/LoaiSanPham.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiSanPham extends Model
{
    protected $table = 'loai_sanpham';
    protected $fillable = ['sanpham_id', 'loai_id'];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }

    public function loai()
    {
        return $this->belongsTo(Loai::class, 'loai_id');
    }
}

<?php
// app/Models/YeuThich.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuThich extends Model
{
    protected $table = 'yeuthich';
    protected $fillable = ['sanpham_id','nguoidung_id','thoi_gian_them'];

    public function user()
    {
        return $this->belongsTo(NguoiDung::class,'nguoidung_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class,'sanpham_id');
    }
}

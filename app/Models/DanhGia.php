<?php
// app/Models/DanhGia.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danhgia';
    protected $fillable = [
  'nguoidung_id', 'san_pham_id', 'noi_dung', 'sosao',
  'ngaydanhgia', 'reply', 'is_replied','trang_thai',
];

    public function user()
    {
        return $this->belongsTo(NguoiDung::class,'nguoidung_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class,'san_pham_id');
    }
}

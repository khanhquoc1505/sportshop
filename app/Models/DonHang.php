<?php
// app/Models/DonHang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'donhang';
    protected $fillable = [
        'nguoidung_id','sanpham_id','madon','ngaydat',
        'tongtien','trangthaidonhang','trangthai','thoigianthem','soluong'
    ];

    public function user()
    {
        return $this->belongsTo(NguoiDung::class,'nguoidung_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class,'sanpham_id');
    }
}

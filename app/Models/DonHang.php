<?php
// app/Models/DonHang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'donhang';
    protected $fillable = [
        'nguoidung_id','madon','ngaydat',
        'tongtien','gia_giam','trangthaidonhang','trangthai','thoigianthem','soluong'
    ];

    public function user()
    {
        return $this->belongsTo(NguoiDung::class,'nguoidung_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class,'sanpham_id');
    }
    public function chiTiet()
{
    return $this->hasMany(DonHangSanPham::class, 'donhang_id');
}
}

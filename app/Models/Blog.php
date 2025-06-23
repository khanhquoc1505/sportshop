<?php
// app/Models/Blog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';
    protected $fillable = [
        'tieude','noidung','trangthai',
        'hinhdaidien','thoi_gian_them','thoi_gian_cap_nhat','sanpham_id'
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class,'sanpham_id');
    }
}

<?php
// app/Models/Loai.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loai extends Model
{
    protected $table = 'loai';
    protected $fillable = ['loai',  'status',];

    public function sanPhamPivot()
    {
        return $this->hasMany(LoaiSanPham::class, 'loai_id');
    }
    protected $casts = [
      'status' => 'boolean',
    ];
    public function sanphams()
    {
        return $this->belongsToMany(
            SanPham::class,      // Model đích
            'loai_sanpham',      // Tên pivot table
            'loai_id',           // Khóa ngoại trên pivot trỏ về Loai
            'sanpham_id'         // Khóa ngoại trên pivot trỏ về SanPham
        );
    }
}

<?php
// app/Models/GioiTinh.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioiTinh extends Model
{
    protected $table = 'gioitinh';
    protected $fillable = ['gioitinh'];

    public function sanPhamPivot()
    {
        return $this->hasMany(GioiTinhSanPham::class, 'gioitinh_id');
    }
}

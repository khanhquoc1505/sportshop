<?php
// app/Models/Loai.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loai extends Model
{
    protected $table = 'loai';
    protected $fillable = ['loai'];

    public function sanPhamPivot()
    {
        return $this->hasMany(LoaiSanPham::class, 'loai_id');
    }
    
}

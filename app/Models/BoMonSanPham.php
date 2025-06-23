<?php
// app/Models/BoMonSanPham.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoMonSanPham extends Model
{
    protected $table = 'bomon_sanpham';
    protected $fillable = ['sanpham_id', 'bomon_id'];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }

    public function boMon()
    {
        return $this->belongsTo(BoMon::class, 'bomon_id');
    }
}

<?php
// app/Models/SanPhamKichCoMauSac.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPhamKichCoMauSac extends Model
{
    protected $table = 'sanpham_kichco_mausac';
    protected $fillable = ['sanpham_id','kichco_id','mausac_id','sl','hinh_anh','trang_thai',];

    public function product()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id', 'id');
    }

    public function kichCo()
    {
        return $this->belongsTo(KichCo::class, 'kichco_id', 'id');
    }

    public function mauSac()
    {
        return $this->belongsTo(MauSac::class, 'mausac_id', 'id');
    }
}

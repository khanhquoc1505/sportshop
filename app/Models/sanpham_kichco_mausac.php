<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sanpham_kichco_mausac extends Model
{
    protected $table = 'sanpham_kichco_mausac';
    protected $fillable = ['sanpham_id','kichco_id','mausac_id','sl','hinh_anh'];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class,'sanpham_id');
    }

    public function kichCo()
    {
        return $this->belongsTo(KichCo::class,'kichco_id');
    }

    public function mauSac()
    {
        return $this->belongsTo(MauSac::class,'mausac_id');
    }
}

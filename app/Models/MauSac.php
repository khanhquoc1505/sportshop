<?php
// app/Models/MauSac.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MauSac extends Model
{
    protected $table = 'mausac';
    protected $fillable = ['mausac'];

    public function sanPhamVariants()
    {
        return $this->hasMany(sanpham_kichco_mausac::class, 'mausac_id');
    }

    public function nhapKhoDetails()
    {
        return $this->hasMany(nhapkho_kichco_mausac::class, 'mausac_id');
    }
}

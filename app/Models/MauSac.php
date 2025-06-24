<?php
// app/Models/MauSac.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MauSac extends Model
{
    protected $table = 'mausac';
    public $timestamps = false;
    protected $fillable = ['mausac'];

    public function sanPhamVariants()
    {
        return $this->hasMany(SanPhamKichCoMauSac::class, 'mausac_id');
    }

    public function nhapKhoDetails()
    {
        return $this->hasMany(nhapkho_kichco_mausac::class, 'mausac_id');
    }
}

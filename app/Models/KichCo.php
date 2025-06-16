<?php
// app/Models/KichCo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KichCo extends Model
{
    protected $table = 'kichco';
    protected $fillable = ['size','loai_size'];

    public function sanPhamVariants()
    {
        return $this->hasMany(sanpham_kichco_mausac::class, 'kichco_id');
    }

    public function nhapKhoDetails()
    {
        return $this->hasMany(nhapkho_kichco_mausac::class, 'kichco_id');
    }
}

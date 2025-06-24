<?php
// app/Models/KichCo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KichCo extends Model
{
    protected $table = 'kichco';
    protected $fillable = ['size','loai_size'];
    public $timestamps = false;

    public function sanPhamVariants()
    {
        return $this->hasMany(SanPhamKichCoMauSac::class, 'kichco_id');
    }

    public function nhapKhoDetails()
    {
        return $this->hasMany(nhapkho_kichco_mausac::class, 'kichco_id');
    }
}

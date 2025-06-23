<?php
// app/Models/BoMon.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoMon extends Model
{
    protected $table = 'bomon';
    protected $fillable = ['bomon'];

    public function sanpham()
    {
        return $this->hasMany(BoMonSanPham::class, 'bomon_id');
    }
    public function sanPhams()
    {
        return $this->belongsToMany(
            SanPham::class,
            'bomon_sanpham',
            'bomon_id',
            'sanpham_id'
        )->withTimestamps();
    }
}

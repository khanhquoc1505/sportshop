<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColorImage extends Model
{
    use HasFactory;

    protected $table = 'color_images'; // Tên bảng trong database

    protected $fillable = [
        'sanpham_id',
        'mausac_id',
        'image_path',
        'is_main',
    ];

    // === Quan hệ đến sản phẩm ===
    public function sanpham()
    {
        return $this->belongsTo(Sanpham::class, 'sanpham_id');
    }

    // === Quan hệ đến màu sắc ===
    public function mausac()
    {
        return $this->belongsTo(Mausac::class, 'mausac_id');
    }
    public function getHinhAnhAttribute()
    {
        return $this->image_path;
    }
}

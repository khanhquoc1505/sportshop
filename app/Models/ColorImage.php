<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColorImage extends Model
{
    use HasFactory;

    protected $table = 'color_images'; // Tên bảng trong database
    public $timestamps = false;

    protected $fillable = [
        'sanpham_id',
        'mausac_id',
        'image_path',
        'is_main',
    ];

    // === Quan hệ đến sản phẩm ===
    public function product()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id', 'id');
    }

    // === Quan hệ đến màu sắc ===
    public function mausac()
    {
        return $this->belongsTo(Mausac::class, 'mausac_id', 'id');
    }
    public function getHinhAnhAttribute()
    {
        return $this->image_path;
    }
}

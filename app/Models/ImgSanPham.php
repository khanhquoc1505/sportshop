<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImgSanPham extends Model
{
    use HasFactory;

    protected $table = 'img_san_phams';

    protected $fillable = [
        'sanpham_id',
        'image_path',
    ];

    /**
     * Quan hệ: ảnh thuộc về một sản phẩm
     */
    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }
}

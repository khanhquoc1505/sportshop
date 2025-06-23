<?php
// app/Models/DanhGia.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danhgia';
    public $timestamps = false;  // bảng không có created_at/updated_at

    protected $fillable = [
         'nguoidung_id',
        'san_pham_id',
        'sosao',
        'noi_dung',
        'hinh_anh',
        'ngaydanhgia',
        'is_replied',
        'reply',
    ];

    /**
     * Cast hinh_anh từ JSON thành array tự động
     * và ngays đánh giá thành datetime
     */
    protected $casts = [
        'hinh_anh'    => 'array',
        'ngaydanhgia' => 'datetime',
        'is_replied'  => 'boolean',
    ];

    /**
     * Người dùng tạo đánh giá
     */
    public function user()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoidung_id');
    }

    /**
     * Sản phẩm được đánh giá
     */
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}

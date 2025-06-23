<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHangSanPham extends Model
{
    protected $table = 'donhang_sanpham';
    public $timestamps = false;  // nếu bạn không có created_at/updated_at

    protected $fillable = [
        'donhang_id',
        'sanpham_id',
        'soluong',
        'dongia',
        'mausac',
        'hinh_anh',
    ];

    public function product()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }
}

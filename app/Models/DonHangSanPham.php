<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\MauSac;
use App\Models\KichCo;

class DonHangSanPham extends Model
{
    protected $table = 'donhang_sanpham';
    public $timestamps = false;  // nếu bạn không có created_at/updated_at

    protected $fillable = [
        'donhang_id',
        'sanpham_id',
        'soluong',
        'dongia',
        'size',
        'mausac',
        'hinh_anh',
    ];

    public function donhang()
    {
        return $this->belongsTo(DonHang::class, 'donhang_id');
    }

    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }

    // Alias nếu cần
    public function product()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }


    public function mauSac()
    {
        // cột `mausac` trong pivot lưu id của bảng mau_sacs
        return $this->belongsTo(MauSac::class, 'mausac', 'id');
    }

    public function kichCo()
    {
        // cột `size` trong pivot lưu size (hoặc id)
        return $this->belongsTo(KichCo::class, 'size', 'size');
    }
    
}

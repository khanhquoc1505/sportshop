<?php
// app/Models/SanPham.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'sanpham';
    protected $fillable = [
        'masanpham','ten','mo_ta','gia_ban',
        'hinh_anh','trang_thai','thoi_gian_them'
    ];

    public function variants()
    {
        return $this->hasMany(sanpham_kichco_mausac::class, 'sanpham_id');
    }

    public function boMons()
    {
        return $this->belongsToMany(
            BoMon::class,'bomon_sanpham','sanpham_id','bomon_id'
        )->withTimestamps();
    }

    public function loais()
    {
        return $this->belongsToMany(
            Loai::class,'loai_sanpham','sanpham_id','loai_id'
        )->withTimestamps();
    }

    public function gioiTinhs()
    {
        return $this->belongsToMany(
            GioiTinh::class,'gioitinh_sanpham','sanpham_id','gioitinh_id'
        )->withTimestamps();
    }

    public function mauSacs()
    {
        return $this->variants()->with('mauSac')->get()->pluck('mauSac')->unique();
    }

    public function kichCos()
    {
        return $this->variants()->with('kichCo')->get()->pluck('kichCo')->unique();
    }

    public function nhapKho()
    {
        return $this->hasMany(NhapKho::class, 'sanpham_id');
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'sanpham_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'sanpham_id');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'san_pham_id');
    }

    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'sanpham_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'sanpham_id');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'sanpham_id');
    }
     public function avatarImage()
{
    return $this->hasOne(ColorImage::class, 'sanpham_id')
                ->where('is_main', true)
                ->orderBy('id');
}
public function colorImages()
{
    return $this->hasMany(ColorImage::class, 'sanpham_id');
}
}

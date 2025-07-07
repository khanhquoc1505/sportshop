<?php
// app/Models/SanPham.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Voucher;
use App\Models\Sanpham_Kichco_Mausac;
use App\Models\BoMon;
use App\Models\Loai;
use App\Models\GioiTinh;
use App\Models\NhapKho;
use App\Models\DonHang;
use App\Models\Comment;
use App\Models\DanhGia;
use App\Models\YeuThich;
use App\Models\Blog;
use App\Models\ColorImage;
use App\Models\ImgSanPham;
use Illuminate\Support\Facades\Auth;

class SanPham extends Model
{
    protected $table = 'sanpham';
    protected $fillable = [
        'masanpham',
        'ten',
        'mo_ta',
        'gia_ban',
        'gia_nhap',
        'gia_buon',
        'hinh_anh',
        'trang_thai',
        'thoi_gian_them'
    ];

    public function variants()
    {
        return $this->hasMany(SanPhamKichCoMauSac::class, 'sanpham_id');
    }
    public function getTongSoLuongAttribute()
    {
        return $this->variants()->sum('sl');
    }

    public function boMons()
    {
        return $this->belongsToMany(
            BoMon::class,
            'bomon_sanpham',
            'sanpham_id',
            'bomon_id'
        )->withTimestamps();
    }

    public function loais()
    {
        return $this->belongsToMany(
            Loai::class,
            'loai_sanpham',
            'sanpham_id',
            'loai_id'
        )->withTimestamps();
    }

    public function gioiTinhs()
    {
        return $this->belongsToMany(
            GioiTinh::class,
            'gioitinh_sanpham',
            'sanpham_id',
            'gioitinh_id'
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
        return $this->belongsToMany(
            Voucher::class,
            'voucher_sanpham',
            'sanpham_id',
            'voucher_id',

        );
    }
    public function avatarImage()
    {
        return $this->hasOne(ColorImage::class, 'sanpham_id')
            ->where('is_main', true)
            ->orderBy('id');
    }
     public function colorImages()
    {
        return $this->hasMany(ColorImage::class, 'sanpham_id', 'id');
    }
    public function images()
    {

        //return $this->hasMany(ImgSanPham::class, 'sanpham_id');

        return $this->hasMany(ColorImage::class, 'sanpham_id', 'id');

    }
    public function getMauSacsAttribute()
    {
        return $this->variants->load('mauSac')->pluck('mauSac')->unique();
    }

    public function getKichCosAttribute()
    {
        return $this->variants->load('kichCo')->pluck('kichCo')->unique();
    }
    public function firstImage()
    {
        return $this->color_images()->first()?->image_path;
    }
    public function loai()
    {
        return $this->belongsToMany(
            Loai::class,       // model Loai
            'loai_sanpham',                // bảng pivot
            'sanpham_id',                  // khóa ngoại trên pivot trỏ về SanPham
            'loai_id'                      // khóa ngoại trên pivot trỏ về Loai
        );
    }
    public function banHang()
    {
        return $this->hasMany(DonhangSanpham::class, 'sanpham_id');
    }
    public function activeVariants()
    {
        return $this->variants()
                    ->where('trang_thai', 1);
    }
    public function isFavorited(): bool
    {
        if (! Auth::check()) {
            return false;
        }
        return $this->yeuThichs()
                    ->where('nguoidung_id', Auth::id())
                    ->exists();
    }
}

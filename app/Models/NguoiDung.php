<?php
// app/Models/NguoiDung.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;

class NguoiDung extends Authenticatable
{
    protected $table = 'nguoidung';
    protected $fillable = [
        'ten_nguoi_dung','mat_khau','email','sdt','dia_chi','avatar','vai_tro','password_enc',
    ];

    public function nhapKho()
    {
        return $this->hasMany(NhapKho::class, 'nguoidung_id');
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'nguoidung_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'nguoidung_id');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'nguoidung_id');
    }

    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'nguoidung_id');
    }
    public function getAuthPassword()
{
    return $this->mat_khau;
}
public function getDecryptedPasswordAttribute()
  {
    try {
      return Crypt::decryptString($this->password_enc);
    } catch (\Exception $e) {
      return null;
    }
  }

  public function getAvatarUrlAttribute()
{
    return $this->avatar
        ? asset($this->avatar)
        : asset('images/avatar-placeholder.png');
}

  public function member()
    {
        return $this->hasOne(Member::class, 'user_id');
    }
public function favorites()
    {
        return $this->belongsToMany(
            SanPham::class,    // model đích
            'yeuthich',        // bảng pivot
            'nguoidung_id',    // FK về user
            'sanpham_id'       // FK về product
        )
        ->withPivot('thoi_gian_them')   // nếu bạn cần lấy thêm cột này
        ->withTimestamps();
    }
}

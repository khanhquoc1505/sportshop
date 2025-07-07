<?php
// app/Models/NguoiDung.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;

class NguoiDung extends Authenticatable
{
    protected $table = 'nguoidung';
    protected $fillable = [
        'ten_nguoi_dung','mat_khau','email','sdt','dia_chi','vai_tro','password_enc',
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
  public function member()
    {
        return $this->hasOne(Member::class, 'user_id');
    }
}

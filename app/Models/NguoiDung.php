<?php
// app/Models/NguoiDung.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

class NguoiDung extends Authenticatable
{
    use Notifiable;
    protected $table = 'nguoidung';
    protected $fillable = [
        'ten_nguoi_dung',
        'mat_khau',
        'email',
        'sdt',
        'dia_chi',
        'avatar',
        'vai_tro',
    ];
    protected $primaryKey = 'id';

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHangChiTiet extends Model
{
    protected $table = 'donhang_chitiets'; // <–– Đổi cho đúng tên table của bạn
    protected $fillable = ['donhang_id','sanpham_id','soluong','dongia'];

    public function donhang()
    {
        return $this->belongsTo(DonHang::class, 'donhang_id');
    }
}

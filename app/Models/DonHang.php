<?php
// app/Models/DonHang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'donhang';
    protected $fillable = [
        'nguoidung_id','sanpham_id','madon','ngaydat',
        'tongtien','trangthaidonhang','trangthai','thoigianthem','soluong', 
        'shipping_method',
        'notes',              
        'discount',           
        'shipping_fee',       
        'paid_amount',        
        'refunded_amount',
        'shipping_method',
        'delivery_status',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(NguoiDung::class,'nguoidung_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class,'sanpham_id');
    }
    public function items()
    {
        return $this->hasMany(DonHangSanPham::class, 'donhang_id');
    }
    public const SHIPPING_METHODS = [
        'standard' => [
            'label'   => 'Giao hàng tiêu chuẩn',
        ],
        'express' => [
            'label'   => 'Giao hàng nhanh',
        ],
        'same_day' => [
            'label'   => 'Giao hàng hỏa tốc',
        ],
        'economy' => [
            'label'   => 'Giao hàng tiết kiệm',
        ],
        'international' => [
            'label'   => 'Vận chuyển quốc tế',
        ],
        'self' => [
            'label'   => 'Tự vận chuyển',
        ],
    ];
    public function getShippingMethodLabelAttribute()
{
    $key = strtolower($this->shipping_method);
    return static::SHIPPING_METHODS[$key]['label'] ?? $this->shipping_method;
}
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    // 1. Override đúng tên bảng trong database
    protected $table = 'danhgia';

    // 2. Nếu bạn không dùng created_at/updated_at mặc định, tắt timestamps
    public $timestamps = false;

    // 3. Đặt đúng tên các cột bạn có trong bảng `danhgia`
    protected $fillable = [
        'nguoidung_id',    // tương ứng với customer_id
        'san_pham_id',     // tương ứng với product_id
        'noi_dung',        // tương ứng với message
        'sosao',           // tương ứng với rating
        'ngaydanhgia',     // tương ứng với created_at custom
        'reply',
        'is_replied',
        'trang_thai'       // nếu bạn có cột trạng thái trả lời
    ];

    // 4. Định nghĩa lại quan hệ với Product/Customer
    public function product()
    {
        // lưu ý dùng class đúng namespace của model sản phẩm
        return $this->belongsTo(\App\Models\SanPham::class, 'san_pham_id');
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\NguoiDung::class, 'nguoidung_id');
    }
}

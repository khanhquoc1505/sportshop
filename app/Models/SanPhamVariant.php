<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPhamVariant extends Model
{
    protected $table = 'sanpham_variants';
    protected $fillable = ['sanpham_id','size','color','quantity'];

    public function product()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }
}

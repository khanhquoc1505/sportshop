<?php
// app/Models/NhapKhoDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class nhapkho_kichco_mausac extends Model
{
    protected $table = 'nhapkho_kichco_mausac';
    protected $fillable = ['nhapkho_id','kichco_id','mausac_id','sl','hinh_anh'];

    public function nhapKho()
    {
        return $this->belongsTo(NhapKho::class,'nhapkho_id');
    }

    public function kichCo()
    {
        return $this->belongsTo(KichCo::class,'kichco_id');
    }

    public function mauSac()
    {
        return $this->belongsTo(MauSac::class,'mausac_id');
    }
}

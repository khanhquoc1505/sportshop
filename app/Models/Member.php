<?php
// app/Models/Member.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';

    protected $fillable = [
        'user_id','membership_tier','is_active','created_at',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'membership_tier' => 'string',
    ];
    public function user()
    {
        return $this->belongsTo(NguoiDung::class,'user_id', 'id');
    }
}

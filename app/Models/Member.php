<?php
// app/Models/Member.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'membership_tier',
        'is_active',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'membership_tier' => 'string',
    ];
}

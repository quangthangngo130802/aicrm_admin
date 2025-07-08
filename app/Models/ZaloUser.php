<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZaloUser extends Model
{
    use HasFactory;
    protected $table = 'zalo_users';

    protected $fillable = [
        'user_id',
        'display_name',
        'admin_id',
        'phone',
        'address'
    ];

    public $timestamps = true;
}

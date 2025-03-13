<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZaloOa extends Model
{
    use HasFactory;

    protected $table = 'sgo_zalo_oas';

    protected $fillable = [
        'name',
        'oa_id',
        'access_token',
        'refresh_token',
        'is_active',
        'user_id',
        'access_token_expiration',
    ];

    // Define a relationship with ZnsMessage
    public function messages()
    {
        return $this->hasMany(ZnsMessage::class, 'oa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

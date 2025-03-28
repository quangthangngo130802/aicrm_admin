<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SuperAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'super_admins';
    protected $guarded = [];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function bankcompany()
    {
        return $this->belongsTo(Bank::class, 'company_bank_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'banner' => 'array'
    ];
}

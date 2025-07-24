<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    use HasFactory;
    protected $table = 'webhooks';

    protected $fillable = [
        'oa_id',
        'user_id',
        'name',
    ];
    public $timestamps = true;
}

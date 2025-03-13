<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $table = 'sgo_transfers';
    protected $fillable = [
        'amount',
        'user_id',
        'notification'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

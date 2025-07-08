<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ggsheet extends Model
{
    use HasFactory;

    protected $table = 'ggsheets'; // tên bảng trong CSDL

    protected $fillable = [
        'user_id',
        'api_code',
        'name_sheet'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

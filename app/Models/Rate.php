<?php

namespace App\Models;

use App\Models\OaTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $table = 'rates';

    protected $fillable = [
        'user_id',
        'template_id',
        'note',
        'rate',
        'submitDate',
        'msgId',
        'feedbacks',
        'oaId',
    ];

    protected $casts = [
        'submitDate' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(OaTemplate::class, 'template_id', 'template_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationRate extends Model
{
    use HasFactory;

    protected $table = 'sgo_automation_rate';

    protected $fillable = [
        'name',
        'template_id',
        'user_id',
        'status',
        'start_time',
        'numbertime'
    ];

    public function template()
    {
        return $this->belongsTo(OaTemplate::class, 'template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

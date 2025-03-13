<?php

namespace App\Models;

use App\Models\OaTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationBirthday extends Model
{
    use HasFactory;
    protected $table = 'sgo_automation_birthday'; // Tên bảng

    protected $fillable = [
        'name',
        'status',
        'start_time',
        'template_id',
        'user_id'
    ];
    public function template()
    {
        return $this->belongsTo(OaTemplate::class, 'template_id');
    }
}

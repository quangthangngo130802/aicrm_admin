<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationReminder extends Model
{
    use HasFactory;
    protected $table = 'sgo_automation_reminder';

    // Các trường được phép cập nhật
    protected $fillable = [
        'name',
        'status',
        'sent_time',
        'template_id',
        'numbertime',
        'user_id'
    ];

    // Định nghĩa quan hệ với bảng templates
    public function template()
    {
        return $this->belongsTo(OaTemplate::class, 'template_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationUser extends Model
{
    use HasFactory;
    protected $table = 'sgo_automation_user'; // Tên bảng trong cơ sở dữ liệu

    protected $fillable = [
        'name',
        'template_id',
        'status',
        'user_id',
    ];

    // Nếu bạn muốn thiết lập quan hệ với Template
    public function template()
    {
        return $this->belongsTo(OaTemplate::class, 'template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

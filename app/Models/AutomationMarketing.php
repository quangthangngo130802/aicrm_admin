<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationMarketing extends Model
{
    use HasFactory;
    protected $table = 'sgo_automation_marketing'; // Tên bảng trong cơ sở dữ liệu

    protected $fillable = [
        'name',
        'template_id',
        'status',
    ];

    // Nếu bạn muốn thiết lập quan hệ với Template
    public function template()
    {
        return $this->belongsTo(OaTemplate::class, 'template_id');
    }
}


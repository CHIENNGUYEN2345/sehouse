<?php

namespace Modules\EduMarketing\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\EduMarketing\Models\Classs;
use Modules\ThemeEdu\Models\Student;

class MarketingMailLog extends Model
{

    protected $table = 'marketing_mail_log';

//    public $timestamps = false;

    protected $fillable = [
        'object_id', 'type', 'marketing_mail_id', 'error', 'sent', 'email', 'opened'
    ];

    public function campaign()
    {
        return $this->belongsTo(MaketingMail::class, 'marketing_mail_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'object_id');
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'object_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'object_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'object_id');
    }
}

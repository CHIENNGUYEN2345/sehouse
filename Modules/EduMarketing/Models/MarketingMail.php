<?php

namespace Modules\EduMarketing\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\EduMarketing\Models\Classs;
use Modules\ThemeEdu\Models\Contact;
use Modules\ThemeEdu\Models\Student;

class MarketingMail extends Model
{
    protected $table = 'marketing_mail';
    protected $guarded = [];
//    protected $fillable = [
//        'admin_id','category_id',
//    ];

    public function classs()
    {
        return $this->hasMany(Classs::class, 'class_id', 'id');
    }

    public function student()
    {
        return $this->hasMany(Student::class, 'student_ids', 'id');
    }

    public function maillog()
    {
        return $this->hasMany(MarketingMailLog::class, 'marketing_mail_id', 'id');
    }

    public function maillogActive()
    {
        return $this->hasMany(MarketingMailLog::class, 'marketing_mail_id', 'id')
            ->where('sent', '!=', 1)->where('error', '!=', 0);
    }

    public function email_template()
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }

    public function email_account()
    {
        return $this->belongsTo(EmailAccount::class, 'email_account_id');
    }
}

<?php

namespace Modules\EduMarketing\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\EduMarketing\Models\Classs;
use Modules\ThemeEdu\Models\Admin;
use Modules\ThemeEdu\Models\Center;
use Modules\ThemeEdu\Models\Contact;
use Modules\ThemeEdu\Models\Course;
use Modules\ThemeEdu\Models\Student;

class Customer extends Model
{
    protected $table = 'customer';
    protected $guarded = [];
//    protected $fillable = [
//        'admin_id','category_id',
//    ];

    public function tag()
    {
        return $this->hasMany(Tag::class,'tag','id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class,'admin_id','id');
    }

    public function marketing()
    {
        return $this->belongsTo(MaketingMail::class,'marketing_id','id');
    }

    public function center()
    {
        return $this->belongsTo(Center::class,'center_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class,'course_id','id');
    }
}

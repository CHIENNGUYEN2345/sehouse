<?php

namespace Modules\EduMarketing\Models;


use App\Http\Helpers\CommonHelper;
use DB;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Modules\EduMarketing\Models\Tuition;

class Student extends Model implements AuthenticatableContract
{
    use Authenticatable;
    protected $guard = 'student';
    protected $guard_name = 'student';

    protected $table = 'students';
    protected $fillable = ['code', 'name', 'phone', 'email', 'password', 'source', 'channel', 'user_id', 'center', 'image',
        'banner', 'status', 'facebook', 'zalo', 'gender', 'birthday', 'status', 'facebook_id', 'google_id'
        ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'tel');
    }

    public function quizlog()
    {
        return $this->belongsTo(QuizLog::class, 'quizlog_id', 'id');
    }

    protected $hidden = [
        'password'
    ];

}

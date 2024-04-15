<?php

namespace App\Models;

use DB;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class Student extends Model implements AuthenticatableContract
{
    use Authenticatable;
    protected $guard = 'student';
    protected $guard_name = 'student';

    protected $table = 'students';
    protected $fillable = ['code', 'name', 'phone', 'email', 'password', 'source', 'channel', 'user_id', 'center', 'avatar', 'status', 'facebook', 'zalo', 'gender', 'birthday', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(Admin::class, 'user_id');
    }

    protected $hidden = [
        'password'
    ];

}

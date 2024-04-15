<?php

/**
 * Admin Model
 *
 * Admin Model manages Admin operation.
 *
 * @category   Admin
 * @package    vRent
 * @author     Techvillage Dev Team
 * @copyright  2017 Techvillage
 * @license
 * @version    1.3
 * @link       http://techvill.net
 * @since      Version 1.3
 * @deprecated None
 */

namespace Modules\EduMarketing\Models ;

use App\Http\Helpers\CommonHelper;
use DB;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    protected $table = 'admin';

    protected $fillable = [
        'name', 'email', 'password', 'address', 'phone', 'image', 'gender', 'birthday', 'role_id'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'role_admin', 'admin_id', 'role_id');
    }

    public function getProfileSrcAttribute()
    {
        if (@$this->attributes['image'] == '')
            $src = url('public/images_core/avatar_user_default.png');
        else
            $src = CommonHelper::getUrlImageThumb(@$this->attributes['image'], 25, 25);
        return $src;
    }


}

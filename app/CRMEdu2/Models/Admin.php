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

namespace App\CRMEdu\Models;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{

    protected $table = 'admin';

    protected $fillable = [
        'name', 'email', 'password', 'address', 'tel', 'image', 'gender', 'birthday', 'api_token', 'password_md5', 'fb_id', 'role_id'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'role_admin', 'admin_id', 'role_id');
    }

    public function saler() {
        return $this->belongsTo($this, 'saler_id', 'id');
    }

    public function invite() {
        return $this->belongsTo($this, 'invite_by', 'id');
    }
}

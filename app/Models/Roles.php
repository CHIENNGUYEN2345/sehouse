<?php

/**
 * Roles Model
 *
 * Roles Model manages Roles operation. 
 *
 * @category   Roles
 * @package    vRent
 * @author     Techvillage Dev Team
 * @copyright  2017 Techvillage
 * @license    
 * @version    1.3
 * @link       http://techvill.net
 * @since      Version 1.3
 * @deprecated None
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Roles extends Model
{
    public static function permission_role($id)
    {
        return DB::table('permission_role')->where('role_id', $id)->pluck('permission_id');
    }

    public static function role_admin($id)
    {
        return DB::table('role_admin')->where('admin_id', $id)->first();
    }
    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}

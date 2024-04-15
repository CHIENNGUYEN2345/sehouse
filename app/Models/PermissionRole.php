<?php

/**
 * PermissionRole Model
 *
 * PermissionRole Model manages PermissionRole operation. 
 *
 * @category   PermissionRole
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
class PermissionRole extends Model
{
    protected $table = 'permission_role';
	protected $fillable = ['permission_id', 'role_id'];
    public $timestamps = false;
}

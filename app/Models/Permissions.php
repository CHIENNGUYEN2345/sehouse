<?php

/**
 * Permissions Model
 *
 * Permissions Model manages Permissions operation. 
 *
 * @category   Permissions
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

class Permissions extends Model
{
    protected $table = 'permissions';

    protected $fillable = ['name', 'display_name', 'description'];
}

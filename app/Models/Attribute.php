<?php

/**
 * Page Model
 *
 * Page Model manages page operation.
 *
 * @category   Page
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

class Attribute extends Model
{
    protected $table = 'attributes';

    public $timestamps = false;

    protected $fillable = ['key', 'value', 'table', 'type', 'item_id'];
}

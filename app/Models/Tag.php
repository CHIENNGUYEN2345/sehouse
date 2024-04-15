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

class Tag extends Model
{
    protected $table = 'tags';

    public $timestamps = false;

    protected $fillable = ['name', 'note', 'table', 'color', 'order_no', 'count'];
}

<?php
namespace App\CRMDV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EworkingCompany\Models\Company;
use Modules\EworkingUser\Models\Admin;
use Modules\JdesOrder\Models\Order;

class BillCostList extends Model
{

    use SoftDeletes;
    protected $table = 'bill_cost_list';

    protected $fillable = [
        'admin_id' , 'bill_id' , 'hang_muc' , 'don_gia', 'so_luong'
    ];


}

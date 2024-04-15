<?php
namespace App\CRMDV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EworkingCompany\Models\Company;
use Modules\EworkingUser\Models\Admin;
use Modules\JdesOrder\Models\Order;

class BillFinance extends Model
{

    use SoftDeletes;
    protected $table = 'bill_finance';
    
    protected $fillable = [
        'bill_id' , 'debt' , 'received' , 'total', 'detail'
    ];

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

}

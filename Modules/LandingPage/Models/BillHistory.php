<?php
namespace Modules\LandingPage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EworkingCompany\Models\Company;
use Modules\EworkingUser\Models\Admin;
use Modules\JdesOrder\Models\Order;

class BillHistory extends Model
{

    protected $table = 'bill_histories';
    public  $timestamps  =false;
    protected $fillable = [
        'bill_id' , 'date' , 'price'
    ];

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

}

<?php
namespace Modules\Ticket\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EworkingCompany\Models\Company;
use App\Models\Admin;
use Modules\WebBill\Models\Service;
use Modules\JdesOrder\Models\Order;

class Bill extends Model
{

    protected $table = 'bills';

    protected $fillable = [
        'service_id','receipt_method' , 'user_gender', 'date' , 'coupon_code' , 'note' , 'status' , 'total_price' , 'customer_id', 'service_id', 'user_tel', 'user_name', 'user_email', 'user_address', 'user_wards', 'user_city_id'
    ];

    public function customer() {
        return $this->belongsTo(Admin::class, 'customer_id', 'id');
    }

    public function orders() {
        return $this->hasMany(Order::class, 'order_id', 'id');
    }

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function ldp() {
        return $this->hasOne(Landingpage::class, 'bill_id', 'id');
    }
}

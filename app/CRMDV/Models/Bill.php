<?php
namespace App\CRMDV\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin;
use App\CRMDV\Models\Service;

class Bill extends Model
{
    use SoftDeletes;

    protected $table = 'bills';

    protected $fillable = [
        'service_id','receipt_method' , 'user_gender', 'date' , 'coupon_code' , 'note' , 'status' , 'total_price' , 'customer_id', 'user_tel', 'user_name', 'user_email', 'user_address', 'user_wards', 'user_city_id'
    ];

    public function admin() {
        return $this->belongsTo(Admin::class, 'customer_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function customer() {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function customer_legal() {
        return $this->belongsTo(User::class, 'customer_legal_id', 'id');
    }

    public function saler() {
        return $this->belongsTo(Admin::class, 'saler_id', 'id');
    }

    public function orders() {
        return $this->hasMany(Order::class, 'order_id', 'id');
    }

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function ldp() {
        return $this->hasOne(Landingpage::class, 'bill_id', 'id');
    }

    public function bill_finance() {
        return $this->hasOne(BillFinance::class, 'bill_id', 'id');
    }

    public function bill_progress() {
        return $this->hasOne(BillProgress::class, 'bill_id', 'id');
    }
}

<?php
namespace App\CRMWoo\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class BillProgress extends Model
{

    protected $table = 'bill_progress';
    
    protected $fillable = [
        'bill_id' , 'status' , 'yctk' , 'dh_id', 'kt_id', 'kh_xong_image', 'reminder_customer', 'rate', 'rate_content'
    ];

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function dieu_hanh() {
        return $this->belongsTo(Admin::class, 'dh_id', 'id');
    }

    public function ky_thuat() {
        return $this->belongsTo(Admin::class, 'kt_id', 'id');
    }
}

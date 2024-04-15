<?php
namespace App\CRMWoo\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class BillProgressHistory extends Model
{

    protected $table = 'bill_progress_history';
    
    protected $fillable = [
        'bill_id' , 'admin_id' , 'old_value' , 'new_value', 'note', 'type',
    ];

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

}

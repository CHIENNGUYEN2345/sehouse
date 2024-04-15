<?php

namespace Modules\WebBill\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Timekeeper extends Model
{


    protected $table = 'timekeeper';

    protected $fillable = [
        'admin_id', 'may_cham_cong_id', 'time', 'thoi_gian_muon'
    ];

    public function admin() {
        return $this->belongsTo(Admin::class, 'may_cham_cong_id', 'may_cham_cong_id');
    }
}
 
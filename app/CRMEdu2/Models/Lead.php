<?php

namespace App\CRMEdu\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Admin;

class Lead extends Model
{

    protected $table = 'leads';

    protected $fillable = [
        'id', 'name', 'contacted_log_last'
    ];


    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id');
    }
}

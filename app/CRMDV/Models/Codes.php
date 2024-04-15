<?php

namespace App\CRMDV\Models;

use App\Models\Admin;
use App\Models\Province;
use App\Models\Ward;
use App\Models\District;
use Illuminate\Database\Eloquent\Model;

class Codes extends Model
{

    protected $table = 'codes';

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
    public function project_type() {
        return $this->belongsTo(Project_type::class, 'service_id', 'id');
    }

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function province() {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function district() {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function ward() {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }
}

<?php

namespace App\Custom\Models;

use App\Models\Province;
use Illuminate\Database\Eloquent\Model;

use App\Models\Admin;

class Company extends Model
{

    protected $table = 'company_profile';

    protected $fillable = [

    ];


    public function career() {
        return $this->belongsTo(CompanyCategory::class, 'career_id');
    }

    public function province() {
        return $this->belongsTo(Province::class, 'province_id');
    }
}

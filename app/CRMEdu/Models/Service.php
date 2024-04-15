<?php

namespace App\CRMEdu\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public $timestamps = false;

    protected $table = 'services';

    protected $fillable = [
        'id', 'name_vi', 'account_max','intro'
    ];
   


}

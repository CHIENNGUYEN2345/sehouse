<?php

namespace App\Custom\Models;

use App\CRMDV\Models\Codes;
use App\Models\Province;
use Illuminate\Database\Eloquent\Model;

use App\Models\Admin;

class BaoCaoDanKhach extends Model
{

    protected $table = 'bao_cao_dan_khach';

    protected $fillable = [

    ];


    public function admin() {   //  người tạo
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function code() {    //  tạo cho dự án nào
        return $this->belongsTo(Codes::class, 'code_id');
    }

    public function codes() {
        return $this->belongsTo(\App\CRMDV\Models\Codes::class, 'code_id', 'id');
    }
}

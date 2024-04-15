<?php

namespace App\CRMDV\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class CheckErrorLinkLogs extends Model
{

    protected $table = 'check_error_link_logs';

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

}

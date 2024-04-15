<?php

namespace App\CRMWoo\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class PenaltyTicket extends Model
{

    protected $table = 'penalty_ticket';


    public function staff() {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }
}

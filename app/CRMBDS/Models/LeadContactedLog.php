<?php

namespace App\CRMBDS\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\CRMBDS\Models\Lead;

class LeadContactedLog extends Model
{

    protected $table = 'lead_contacted_log';

    protected $fillable = [
        'title', 'admin_id', 'lead_id', 'note', 'type'
    ];

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function lead() {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}

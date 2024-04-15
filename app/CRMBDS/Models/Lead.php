<?php

namespace App\CRMBDS\Models;

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

    public function source_tag() {
        return $this->belongsTo(Tag::class, 'source', 'id');
    }
}

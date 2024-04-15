<?php

namespace App\CRMDV\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LinkErrorLogs extends Model
{
    protected $table = 'check_error_link_logs';
    protected $guarded = [];
    public function domain()
    {
        return $this->hasOne(DomainCheck::class, 'id', 'domain_id');
    }
}

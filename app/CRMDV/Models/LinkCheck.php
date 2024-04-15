<?php

namespace App\CRMDV\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LinkCheck extends Model
{
    protected $table = 'check_error_link';
    protected $guarded = [];
    public $timestamps = false;
    public function domain()
    {
        return $this->hasOne(DomainCheck::class, 'id', 'domain_id');
    }
}

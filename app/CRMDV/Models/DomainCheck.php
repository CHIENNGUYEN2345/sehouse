<?php

namespace App\CRMDV\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomainCheck extends Model
{
    protected $table = 'check_error_domain';
    protected $guarded = [];
    public $timestamps = false;


    public function link()
    {
        return $this->hasMany(LinkCheck::class, 'domain_id', 'id');
    }

    public function delete()
    {
        $this->link()->delete();
        parent::delete();
    }
}

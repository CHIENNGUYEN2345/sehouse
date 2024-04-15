<?php

namespace App\CRMDV\Models;

use Illuminate\Database\Eloquent\Model;

class Project_type extends Model
{

    protected $table = 'project_type';
    public $timestamps = false;

    protected $fillable = [
        'name', 'status', 'slug', 'meta_title', 'meta_keywords', 'meta_description', 'type','color','admin_id','order_no'
    ];
}

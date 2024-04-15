<?php

namespace App\CRMWoo\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    protected $table = 'tags';
    public $timestamps = false;

    protected $fillable = [
        'name', 'status', 'slug', 'meta_title', 'meta_keywords', 'meta_description', 'type'
    ];
}

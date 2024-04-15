<?php

namespace App\CRMBDS\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $table = 'posts';

    protected $fillable = [
        'name', 'link', 'level', 'order_no'
    ];
}

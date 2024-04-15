<?php

namespace Modules\STBDProduct\Models;

use Illuminate\Database\Eloquent\Model;

class Guarantees extends Model
{

    protected $table = 'guarantees';

    protected $fillable = [
        'name','id'
    ];
    public $timestamps =false;
}

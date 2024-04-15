<?php

namespace App\CRMBDS\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class Remind extends Model
{
    public $timestamps = false;

    protected $table = 'reminds';

    public function reminded()
    {
        return $this->hasMany(Admin::class, 'reminded', 'id');
    }
}

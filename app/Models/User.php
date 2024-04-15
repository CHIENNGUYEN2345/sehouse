<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'users';

    protected $guarded=[];

    public function saler() {
        return $this->belongsTo(Admin::class, 'sale_id', 'id');
    }

}

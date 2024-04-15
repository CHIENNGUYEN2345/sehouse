<?php

namespace App\CRMDV\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class Phong_ban extends Model
{

    protected $table = 'phong_ban';
    protected $fillable = [
        'name',
        'image',
        'description',
    ];




    public function admin() {
        return $this->hasMany(Admin::class, 'phong_ban_id', 'id');
    }
}

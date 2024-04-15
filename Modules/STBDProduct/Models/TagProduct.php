<?php
namespace Modules\STBDProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagProduct extends Model
{

    protected $table = 'categories';



    protected $fillable = [
        'name', 'slug','content', 'intro','image', 'parent_id','status', 'intro', 'content'
    ];



}

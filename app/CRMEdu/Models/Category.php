<?php

namespace App\CRMEdu\Models ;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'categories';

    protected $fillable = [
        'name' , 'slug' , 'parent_id' , 'intro' , 'image' , 'user_id' , 'status', 'type', 'order_no', 'created_at', 'link', 'website_id'
    ];

    public function parent()
    {
        return $this->hasOne($this, 'id', 'parent_id');
    }

    public function posts() {
        return $this->hasMany(Post::class, 'category_id', 'id');
    }

    public function exps() {
        return $this->hasMany(Exp::class, 'category_id', 'id');
    }

    public function childs()
    {
        return $this->hasMany($this, 'parent_id', 'id')->orderBy('order_no', 'asc');
    }

    public function childsMenu()
    {
        return $this->hasMany($this, 'parent_id', 'id')->whereIn('type', [0,2])->orderBy('order_no', 'asc');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getImageUrlAttribute()
    {
        return url('/').'/public/filemanager/userfiles/slides/'.$this->attributes['banner'];
    }
}

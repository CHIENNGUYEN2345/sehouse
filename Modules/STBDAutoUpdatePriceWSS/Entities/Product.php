<?php

namespace Modules\STBDAutoUpdatePriceWSS\Entities;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $guarded = [];
    public function website(){
        return $this->belongsTo(Website::class,'website_id','id');
    }
    public function category(){
        return $this->belongsTo(Category::class,'multi_cat','id');
    }
}

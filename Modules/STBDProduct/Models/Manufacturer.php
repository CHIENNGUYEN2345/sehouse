<?php

namespace Modules\STBDProduct\Models;


use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $table = 'manufactureres';
    protected $guarded = [];
    public function category() {
    return $this->belongsTo(Category::class, 'category_id');
}
    public function products() {
        return $this->hasMany(Product::class, 'manufacture_id', 'id');
    }

}
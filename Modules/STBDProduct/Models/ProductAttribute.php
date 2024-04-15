<?php

namespace Modules\STBDProduct\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{

    protected $table = 'product_attributes';
    public $timestamps = false;

    protected $fillable = [
        'product_id' , 'properties_value_ids' , 'image' , 'final_price'
    ];

    public function product_id() {
        return $this->belongsTo(Product::class, 'product_id');
    }

}

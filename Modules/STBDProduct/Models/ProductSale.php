<?php
/**
 * Created by PhpStorm.
 * User: hoanghung
 * Date: 14/05/2016
 * Time: 22:13
 */
namespace Modules\STBDProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSale extends Model {

    protected $table = 'product_sale';
    public function product() {
        return $this->belongsTo(Product::class, 'id_product_sale');
    }
}
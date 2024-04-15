<?php
namespace App\CRMBDS\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\JdesProduct\Models\Product;

class Order extends Model
{

    protected $table = 'orders';


    protected $fillable = [
        'price', 'quantity', 'product_name', 'product_price', 'product_image', 'status'
    ];


    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

}

<?php
namespace App\CRMBDS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillHistory extends Model
{
    use SoftDeletes;

    protected $table = 'bill_histories';

    protected $fillable = [
        'bill_id' , 'date' , 'price'
    ];

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

}

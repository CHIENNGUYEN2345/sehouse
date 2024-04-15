<?php
namespace Modules\WebBill\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\EworkingUser\Models\Admin;

class Plan extends Model
{

    protected $table = 'plans';
    
    protected $fillable = [
        'admin_id' , 'khqt' , 'khqt_cao', 'co_hoi', 'hd', 'ds'
    ];

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

}

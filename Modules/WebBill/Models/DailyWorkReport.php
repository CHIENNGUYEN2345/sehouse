<?php
namespace Modules\WebBill\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class DailyWorkReport extends Model
{

    protected $table = 'daily_work_report';

    protected $fillable = [
        'admin_id' , 'admin_name' , 'admin_code', 'room_id', 'room_name', 'date', 'tao_moi', 'tuong_tac', 'khqt', 'khqt_moi', 'khqt_cao'
    ];

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

}

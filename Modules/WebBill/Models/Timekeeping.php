<?php

namespace Modules\WebBill\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Timekeeping extends Model
{


    protected $table = 'timekeepings';

    protected $fillable = [
        'day', 'start', 'end', 'job_other', 'note', 'time', 'admin_id', 'log_text',
    ];

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}

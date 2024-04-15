<?php
namespace Modules\Ticket\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\LandingPage\Models\Service;

class Ticket extends Model
{

    protected $table = 'tickets';

    protected $fillable = [
        'name', 'content', 'title','status',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
}

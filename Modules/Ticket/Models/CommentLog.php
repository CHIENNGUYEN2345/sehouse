<?php
namespace Modules\Ticket\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\LandingPage\Models\Service;
use phpDocumentor\Reflection\Types\This;

class CommentLog extends Model
{

    protected $table = 'comment_logs';

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function reply()
    {
        return $this->belongsTo(This::class, 'id', 'reply');
    }
}

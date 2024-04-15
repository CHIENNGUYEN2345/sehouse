<?php
namespace Modules\LandingPage\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class Landingpage extends Model
{

    protected $table = 'landingpages';


    protected $fillable = [
        'name', 'ladi_link', 'domain', 'form_action', 'created_at', 'updated_at', 'form_fields', 'customer_id'
    ];

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function customer() {
        return $this->belongsTo(Admin::class, 'customer_id', 'id');
    }

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function career() {
        return $this->belongsTo(Category::class, 'career_id', 'id');
    }
}

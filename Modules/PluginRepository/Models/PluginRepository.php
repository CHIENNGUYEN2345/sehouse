<?php
namespace Modules\PluginRepository\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Modules\ThemeSemicolonwebJdes\Models\Company;

class PluginRepository extends Model
{

    protected $table = 'plugins';

    protected $fillable = [
        'name', 'code', 'intro', 'author', 'image', 'actived', 'image', 'version_required', 'path','link_detail','created_at','updated_at','status'
    ];

//    public function admin(){
//        return $this->belongsTo(Admin::class,'admin_id');
//    }
//    public function company(){
//        return $this->belongsTo(Company::class,'company_id');
//    }


}

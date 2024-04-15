<?php
/**
 * Created by PhpStorm.
 * User: hoanghung
 * Date: 14/05/2016
 * Time: 22:13
 */
namespace Modules\STBDProduct\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {

    protected $table = 'posts';

    protected $fillable = [
        'name', 'slug', 'user_id', 'intro', 'content', 'status', 'image', 'category_id', 'video', 'order_no',
        'important', 'data', 'updated_at', 'tags', 'show_home'
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user() {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function product() {
        return $this->hasMany(Product::class, 'product_sidebar','id');
    }
}
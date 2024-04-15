<?php

namespace Modules\STBDProduct\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Modules\ThemeSTBD\Models\Author;
use Modules\ThemeSTBD\Models\User;

class Product extends Model
{

    protected $table = 'products';

    protected $fillable = [
        'name', 'slug', 'user_id', 'intro', 'content', 'status', 'image', 'category_id', 'video', 'order_no',
        'important', 'data', 'tac_gia', 'base_price','final_price', 'tags','origin_id', 'guarantee', 'international_Code',
        'proprerties_id','meta_robot'
    ];

    protected $appends = ['first_category'];

    public function getFirstCategoryAttribute()
    {
        try {
            $cat_ids = explode('|', $this->attributes['multi_cat']);
            $cat = Category::whereIn('id', $cat_ids)->where('status', 1)->first();
            if (!is_object($cat)) {
                return Category::where('status', 1)->first();
            }
            return $cat;
        } catch (\Exception $ex) {
            return null;
        }
    }
    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function manufacture() {
        return $this->belongsTo(Manufacturer::class, 'manufacture_id');
    }
    public function property_value() {
        return $this->belongsTo(PropertyValue::class, 'proprerties_id');
    }

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function author() {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function post() {
        return $this->belongsTo(Post::class, 'product_sidebar','id');
    }
    public function guarantees() {
        return $this->belongsTo(Guarantees::class,'guarantee');
    }
    public function origin() {
        return $this->belongsTo(Origin::class, 'origin_id');
    }
    public function propertie_value() {
        return $this->belongsTo(PropertieValue::class, 'proprerties_id', 'id');
    }

    public function main_category() {
        try {
            $multi_cat = $this->attributes['multi_cat'];
            $category_id = explode('|', $multi_cat)[1];
            return Category::find($category_id);
        } catch (\Exception $ex) {
            Category::first();
        }
    }

    public function FacturereProduct() {
        return $this->hasMany(FacturereProduct::class, 'product_id', 'id');
    }

    public function childs() {
        return $this->hasMany($this, 'parent_id', 'id');
    }
}

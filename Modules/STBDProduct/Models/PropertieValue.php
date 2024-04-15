<?php

namespace Modules\STBDProduct\Models;

use Illuminate\Database\Eloquent\Model;

class PropertieValue extends Model
{
    protected $table = 'properties_value';
    protected $fillable = ['name', 'value','properties_name_id'];

    public function property_name() {
        return $this->belongsTo(PropertieName::class, 'properties_name_id');
    }

}

<?php

namespace App\Custom\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Admin;

class CompanyCategory extends Model
{

    protected $table = 'company_category';

    protected $fillable = [
        'name'
    ];
}

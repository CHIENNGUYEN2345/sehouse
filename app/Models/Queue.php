<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Queue extends Model
{

    protected $table = 'queue';

    protected $fillable = [
        'queue', 'payload', 'attempts','reserved_at','available_at','created_at'
    ];



}

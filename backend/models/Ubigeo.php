<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{
    protected $table = 'ubigeo_districts';
    protected $keyType = 'string';
    
    protected $guarded = [];
    
    public $timestamps = false;
}

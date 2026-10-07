<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// (Separado de models/User.php triple, phase2-modular; idéntico.)

class Tdatosu extends Model
{
    protected $table = 'tdatosu';
    protected $primaryKey = 'idD';
    public $timestamps = false;
    protected $guarded = [];
}

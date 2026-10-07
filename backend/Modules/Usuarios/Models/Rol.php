<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Tabla `rol` CENTECPC (idéntico al separado de models/User.php triple).

class Rol extends Model
{
    protected $table = 'rol';
    public $timestamps = false;
    protected $guarded = [];
}

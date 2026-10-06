<?php

namespace CrediSoporte\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'tcaja_usu_detal';
    protected $primaryKey = 'idCAD';
    public $timestamps = false;

    protected $guarded = [];
}
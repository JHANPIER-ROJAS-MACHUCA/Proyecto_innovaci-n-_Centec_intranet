<?php

namespace CrediSoporte\Domain\Models;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';
    public $timestamps = false;

    protected $guarded = [];
}

<?php

namespace CrediSoporte\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dictionary extends Model
{
    use SoftDeletes;

    protected $table = 'dictionaries';
    public $timestamps = false;
}

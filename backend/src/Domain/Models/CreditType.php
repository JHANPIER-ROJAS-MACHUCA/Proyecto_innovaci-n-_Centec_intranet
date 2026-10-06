<?php

namespace CrediSoporte\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'credit_types';
    protected $guarded = [];
    public $timestamps = false;
}

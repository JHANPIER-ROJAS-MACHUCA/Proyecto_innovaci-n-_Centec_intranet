<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'non_payment_justifications';
    public $timestamps = false;

    protected $guarded = [];
}

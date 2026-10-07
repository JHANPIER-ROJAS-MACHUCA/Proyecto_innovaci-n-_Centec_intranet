<?php

namespace App\Models;

use App\Helpers\Holiday;
use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $table = 'goals';
    public $timestamps = false;

    protected $guarded = [];
}

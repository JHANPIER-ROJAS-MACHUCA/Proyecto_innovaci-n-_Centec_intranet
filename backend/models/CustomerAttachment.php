<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAttachment extends Model
{
    protected $table = 'customer_attachments';
    public $timestamps = false;

    protected $guarded = [];
}

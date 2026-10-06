<?php

namespace CrediSoporte\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    protected $table = 'relations';
    protected $guarded = [];
    public $timestamps = false;

    public static function typeToString($type)
    {
        switch ($type) {
            case 'spouse':
                return "Conyuge";
                break;

            case 'endorsement':
                return "Aval";
                break;

            default:
                return "";
                break;
        }
    }

    public function relationable()
    {
        return $this->morphTo();
    }
}

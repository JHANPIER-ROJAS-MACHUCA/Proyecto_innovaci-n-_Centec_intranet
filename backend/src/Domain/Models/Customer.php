<?php

namespace CrediSoporte\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'tclie_general';
    protected $primaryKey = 'idCG';
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        // 'coordinates' => 'json'
    ];

    public function riskProfile()
    {
        return $this->belongsTo(Dictionary::class);
    }

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class);
    }

    public function attachments()
    {
        return $this->hasMany(CustomerAttachment::class, 'customer_id');
    }

    public function civilStatusToString()
    {
        switch ($this->estado_civil) {
            case 'S':
                return 'Soltero';
            case 'C':
                return 'Casado';
            case 'V':
                return 'Viudo';
            case 'D':
                return 'Divorciado';
            case 'Conv':
                return 'Conviviente';
            case 'Sep':
                return 'Separado';

            default:
                return $this->estado_civil;
                break;
        }
    }

    public function portfolio()
    {
        return $this->belongsTo(User::class, "idU");
    }

    public function credits()
    {
        return $this->hasMany(Credit::class, "idCG");
    }

    public function relations()
    {
        return $this->morphedByMany(Customer::class, 'relationable', 'relations', 'customer_id')
            ->withPivot('type');
    }

    public function relationTypeToString($relationType)
    {
        switch ($relationType) {
            case 'spouce':
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
}

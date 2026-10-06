<?php

namespace CrediSoporte\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'tusuario';
    protected $primaryKey = 'idU';
    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = [
        'pass',
    ];

    public function scopeActive($query)
    {
        return $query->where('estadoU', 1);
    }

    public function can($accion)
    {
        switch ($accion) {
            case 'create_attachment':
            case 'update_attachment':
            case 'see_all_attachments':
            case 'update_attachment':
            case 'delete_attachment':
                if ($this->tipoU === '1' || $this->tipoU === '2' || $this->tipoU === '3') {
                    return true;
                }

                return false;
        }

        return false;
    }

    public function credits()
    {
        return $this->hasMany('CrediSoporte\Domain\Models\Credit', 'user_id', 'idU');
    }

    public function fullName()
    {
        return $this->apU . ' ' . $this->amU . ' ' . $this->nomU;
    }
}

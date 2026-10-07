<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Tabla identidad CENTECPC: tusuarios (credencial+rol) + tdatosu (datos personales).
// idRol usa códigos CENTECPC: 1 Super Admin, 2 Asesor, 3 Cliente, 4 Postulante,
// 5 admin_personal, 6 Soporte Web, 7 Plataforma, 8 Gerente, 9 Seguimiento.

class User extends Model
{
    protected $table = 'tusuarios';
    protected $primaryKey = 'idU';
    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = [
        'pass',
    ];

    public function datos()
    {
        return $this->hasOne(Tdatosu::class, 'idU', 'idU');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idRol', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('idEstado', 1);
    }

    public function can($accion)
    {
        switch ($accion) {
            case 'create_attachment':
            case 'update_attachment':
            case 'see_all_attachments':
            case 'update_attachment':
            case 'delete_attachment':
                if (in_array((string)$this->idRol, ['8', '5', '7'], true)) {
                    return true;
                }

                return false;
        }

        return false;
    }

    public function credits()
    {
        return $this->hasMany('App\Models\Credit', 'user_id', 'idU');
    }

    public function fullName()
    {
        $d = $this->datos;
        if ($d) return $d->apU . ' ' . $d->amU . ' ' . $d->nomU;
        return $this->userU;
    }
}

class Tdatosu extends Model
{
    protected $table = 'tdatosu';
    protected $primaryKey = 'idD';
    public $timestamps = false;
    protected $guarded = [];
}

class Rol extends Model
{
    protected $table = 'rol';
    public $timestamps = false;
    protected $guarded = [];
}

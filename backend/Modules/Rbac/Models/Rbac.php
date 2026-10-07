<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// RBAC: Usuario → Rol → Permisos → Módulos → Vistas → Acciones.
// Rol canónico = rbac_roles.codigo = tusuarios.idRol (códigos CENTECPC tabla `rol`).

class RbacRol extends Model
{
    protected $table = 'rbac_roles';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
}

class RbacPermiso extends Model
{
    protected $table = 'rbac_permisos';
    public $timestamps = false;
    protected $guarded = [];
}

class RbacRolPermiso extends Model
{
    protected $table = 'rbac_rol_permiso';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
}

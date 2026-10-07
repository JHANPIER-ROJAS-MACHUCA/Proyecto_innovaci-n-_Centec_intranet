<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Origen: C:\xampp\htdocs\CENTECPC\app\Modules\Sucursales (SucursalesModel + PagoCliente)
// Tablas: sucursales, empresas, bank_accounts, sucursal_metodo_pago,
// tipos_credito, tipos_ahorro, tipos_seguro, pago_comprobantes, usuario_sucursal.

class Sucursal extends Model
{
    protected $table = 'sucursales';
    protected $primaryKey = 'idS';
    public $timestamps = false;
    protected $guarded = [];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}

class Empresa extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}

class BankAccount extends Model
{
    protected $table = 'bank_accounts';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}

class SucursalMetodoPago extends Model
{
    protected $table = 'sucursal_metodo_pago';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}

class PagoComprobante extends Model
{
    protected $table = 'pago_comprobantes';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}

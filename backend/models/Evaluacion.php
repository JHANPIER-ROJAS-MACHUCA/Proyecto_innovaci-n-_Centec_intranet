<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Origen: C:\xampp\htdocs\CENTECPC\app\Modules\Evaluacion\models\Evaluacion.php
// Tablas eval_credito / eval_indicadores / eval_actividades / eval_costos /
// eval_deudas / eval_gastos / eval_ingresos / eval_activos.
// Clave de negocio: `grupo` (timestamp ms generado en crear()).

class EvalCredito extends Model
{
    protected $table = 'eval_credito';
    protected $primaryKey = 'grupo';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}

class EvalIndicador extends Model
{
    protected $table = 'eval_indicadores';
    protected $primaryKey = 'id_indicador';
    public $timestamps = false;
    protected $guarded = [];
}

class EvalActividad extends Model
{
    protected $table = 'eval_actividades';
    protected $primaryKey = 'id_actividad';
    public $timestamps = false;
    protected $guarded = [];
}

class EvalCosto extends Model
{
    protected $table = 'eval_costos';
    protected $primaryKey = 'id_costo';
    public $timestamps = false;
    protected $guarded = [];
}

class EvalDeuda extends Model
{
    protected $table = 'eval_deudas';
    protected $primaryKey = 'id_deuda';
    public $timestamps = false;
    protected $guarded = [];
}

class EvalGasto extends Model
{
    protected $table = 'eval_gastos';
    protected $primaryKey = 'id_gasto';
    public $timestamps = false;
    protected $guarded = [];
}

class EvalIngreso extends Model
{
    protected $table = 'eval_ingresos';
    protected $primaryKey = 'id_ingreso';
    public $timestamps = false;
    protected $guarded = [];
}

class EvalActivo extends Model
{
    protected $table = 'eval_activos';
    protected $primaryKey = 'id_activo';
    public $timestamps = false;
    protected $guarded = [];
}

<?php

namespace CrediSoporte\Modules\Cash\Legacy;

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/registrarAhorro.php.
// El wrapper en app/api/registrarAhorro.php preserva URL, entradas y salida legacy.
class RegistrarAhorro
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

$cash = null;
if ($_COOKIE['tuser'] == 2) {
    $cash = $database->table('tcaja_usuario')
        ->where('idU', $_COOKIE['user1'])
        ->whereNull('montoFin')
        ->whereNull('hfin')
        ->where('tipo', 1)
        ->orderBy('idCA', 'desc')
        ->first();
} else {
    $cash = $database->table('tcaja_usuario')
        ->where('idU', $_COOKIE['user1'])
        ->whereNull('montoFin')
        ->whereNull('hfin')
        ->where('tipo', null)
        ->orderBy('idCA', 'desc')
        ->first();
}

if (!$cash) {
    echo json_encode([
        'message' => 'No tienes habilitado la caja.',
        'success' => false
    ]);
    die();
}

if (!Customer::where('idCG', $request->customer_id)->exists()) {
    echo json_encode([
        'message' => 'El cliente no exite',
        'success' => false
    ]);
    die();
}

$ahorro = $database->table('tahorro')->where('id', $request->customer_id)->first();
$montoDisponible = 0;

if ($ahorro) {
    $s = $database->table('tahorro_deta')
        ->selectRaw('sum(if(tahorro_deta.tipo = 7,tahorro_deta.monto,0)) - sum(if(tahorro_deta.tipo = 8,tahorro_deta.monto,0)) as saldo')
        ->where('tahorro_deta.idA', $ahorro->idA)
        ->where('tahorro_deta.estad', 1)
        ->first();

        $montoDisponible = $s->saldo ?? 0;
}

$maxMonto = $request->tipo == '7' ? '999999' : $montoDisponible;

$errors = $request->validate([
    'tipo' => 'required|in:7,8',
    'motivo' => 'required',
    'monto' => 'required|numeric|max:' . $maxMonto
]);

if (count($errors) > 0) {
    echo json_encode([
        'errors' => $errors,
        'message' => 'Error de validación.',
        'success' => false
    ]);
    die();
}
// fin de la validacion

$ahorroId = $ahorro ? $ahorro->idA : '';
if (!$ahorroId) {
    $ahorroId = $database->table('tahorro')->insertGetId([
        'tipoA' => 1, // ¿? valor desconocido
        'id' => $request->customer_id,
        'motivo' => 11, // ahorro
        'estado' => 1
    ]);
}

$ahorroDetaId = $database->table('tahorro_deta')->insertGetId([
    'idA' => $ahorroId,
    'monto' => $request->monto,
    'fecha' => date('Y-m-d H:i:s'),
    'tipo' => $request->tipo, //ahorro o retiro
    'moti' => $request->motivo,
    'idU' => $_COOKIE['user1'],
    'estad' => 1,
    'balance' => $request->tipo == '7' ? $montoDisponible + $request->monto : $montoDisponible - $request->monto
]);

$database->table('tcaja_usu_detal')->insert([
    'idCA' => $cash->idCA,
    'tipo' => $request->motivo,
    'cuota' => $request->monto,
    'idCuota' => $ahorroDetaId,
    'total' => $request->monto,
    'cliente' => $request->customer_id,
    'created_at' => date('Y-m-d H:i:s')
]);


echo json_encode([
    'transactionId' => $ahorroDetaId,
    'message' => 'Registrado con exito!!',
    'success' => true,
]);
    }
}

<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/confirmarPrestamo.php.
// El wrapper en app/api/confirmarPrestamo.php preserva URL, entradas y salida legacy.
class ConfirmarPrestamo
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

// solo gerencia y administracion pueden desembolsar
if ($_COOKIE['tuser'] != 1 && $_COOKIE['tuser'] != 2) {
    echo json_encode([
        'message' => 'Tu usuario no puede confirmar prestamos.',
        'success' => false
    ]);
    die();
}

// los administradores son los unicos en aprobar creditos mayores a 1500
if ($request->post('edit_montoa') > 1500 && $_COOKIE['tuser'] != 1) {
    echo json_encode([
        'message' => 'No tienes permisos para aprobar montos mayores a S/ 1,500.00',
        'success' => false
    ]);
    die();
}

// el administrador aprueba prestamos sin verificación de caja.
if ($_COOKIE['tuser'] == 1) {
    Credit::where('idP', $request->post('edit_id'))
        ->update([
            'capital' => round(floatval($request->post('edit_montoa')) * 10),
            'interest_rate' => round(floatval($request->post('edit_taza')) * 100) / 100,
            'estado' => 2
        ]);

    echo json_encode([
        'message' => 'Credito aprobado.',
        'success' => true
    ]);
    die();
}

$cash = $database::table('tcaja_usuario')
    ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
    ->where('tcaja_usuario.idU', $_COOKIE['user1'])
    ->where('tcaja_oficina.idO', $_COOKIE['tofi'])
    ->whereNull('tcaja_usuario.montofin')
    ->whereNull('tcaja_oficina.montof')
    ->first();

// la caja de usuarios normales de verifica de otra manera
if ($cash && $cash->tipo != 1 && !$cash->montoIni && !$cash->hini) {
    $cash = null;
}

if (!$cash) {
    echo json_encode([
        'message' => 'No tienes habilitada una caja.',
        'success' => false
    ]);
    die();
}

$saldo = 0;

if ($cash) {
    if ($cash->tipo == 1) { // caja administracion tiene otros ingresos y egresos
        $otrasCajas = $database->table('tcaja_usuario')
            ->where('idCO', $cash->idCO)
            ->where('idCA', '!=', $cash->idCA)
            ->sum('montoFin');

        $designacionTransactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
            ->where('tcaja_usuario.idCO', $cash->idCO)
            ->where('tcaja_usu_detal.tipo', '1')
            ->where('tcaja_usu_detal.habilitacion', '4')
            ->sum('tcaja_usu_detal.monto');

        $saldo += $cash->monto + $otrasCajas - $designacionTransactions;
    }

    $asignacionTransactions = Transaction::where('idCA', $cash->idCA)
        ->where('tipo', 1)
        ->where('estadodt', 2)
        ->where('habilitacion', 4)
        ->sum('monto');

    $cobrosTransactions = Transaction::leftJoin('transaction_details', 'tcaja_usu_detal.idCAD', 'transaction_details.transaction_id')
        ->where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->where('tcaja_usu_detal.tipo', 3)
        ->selectRaw('sum(if(transaction_details.id, tcaja_usu_detal.total,0)) as digital')
        ->selectRaw('sum(if(transaction_details.id, 0,tcaja_usu_detal.total)) as cash')
        ->first();

    $ahorroTransactions = Transaction::join('tahorro_deta', 'tcaja_usu_detal.idCuota', 'tahorro_deta.idAd')
        ->where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->selectRaw('sum(if(tahorro_deta.tipo = 7, tcaja_usu_detal.total, 0)) as income')
        ->selectRaw('sum(if(tahorro_deta.tipo = 8, tcaja_usu_detal.total, 0)) as expense')
        ->first();

    $incomeAndExpense = Transaction::join('tahorro_motivo', 'tcaja_usu_detal.tipo', 'tahorro_motivo.idam')
        ->where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->whereNotIn('tcaja_usu_detal.tipo', [1, 2, 3])
        ->whereNull('tcaja_usu_detal.idCuota')
        ->selectRaw('sum(if(tahorro_motivo.tipoM = 1, tcaja_usu_detal.total, 0)) as income')
        ->selectRaw('sum(if(tahorro_motivo.tipoM = 2, tcaja_usu_detal.total, 0)) as expense')
        ->first();

    $desembolsoTransactions = Transaction::where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->where('tcaja_usu_detal.tipo', 2)
        ->sum('total');

    $saldo += $asignacionTransactions +
        $cobrosTransactions->cash +
        $ahorroTransactions->income -
        $ahorroTransactions->expense +
        $incomeAndExpense->income -
        $incomeAndExpense->expense -
        $desembolsoTransactions;
}

if ($request->post('edit_montoa') > $saldo) {
    echo json_encode([
        'message' => 'No tienes suficiente saldo para confirmar el credito.',
        'success' => false
    ]);
    die();
}


Credit::where('idP', $request->post('edit_id'))
    ->update([
        'capital' => round(floatval($request->post('edit_montoa')) * 10),
        'interest_rate' => round(floatval($request->post('edit_taza')) * 100) / 100,
        'estado' => 2
    ]);

echo json_encode([
    'saldo' => $saldo,
    'message' => 'Credito aprobado.',
    'success' => true
]);
    }
}

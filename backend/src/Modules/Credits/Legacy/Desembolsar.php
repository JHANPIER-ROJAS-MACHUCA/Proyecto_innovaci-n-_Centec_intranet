<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/desembolsar.php.
// El wrapper en app/api/desembolsar.php preserva URL, entradas y salida legacy.
class Desembolsar
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

// solo los administradores y operadores pueden desembolsar
if ($_COOKIE['tuser'] != '2' && $_COOKIE['tuser'] != '3') {
    echo json_encode([
        'message' => 'Lo sentimos, tu usuario no puede desembolsar.',
        'success' => false
    ]);
    die();
}

$creditId = $request->post('id');

$credit = Credit::where('idP', $creditId)
    ->where('estado', 2)
    ->first();

if (!$credit) {
    echo json_encode([
        'success' => false,
        'message' => "El credito no existe."
    ]);
    die();
}

$dateNow = date('Y-m-d');
$cuotasConDeudaMoras = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->where('tprestamo.estado', 4)
    ->where('tprestamo.idCG', $credit->idCG)
    ->whereNull('tpresta_detalle.estado_mora')
    ->where(function ($query) use($dateNow) {
        $query->where(function ($query) {
            $query->where('tpresta_detalle.estado', 1)
                ->whereRaw('tpresta_detalle.fechaProg < date(tpresta_detalle.fechaPago)');
        })->orWhere(function ($query) use($dateNow) {
            $query->where(function ($query) {
                $query->where('tpresta_detalle.estado', 2)
                    ->orWhereNull('tpresta_detalle.estado');
            })
                ->whereRaw("tpresta_detalle.fechaProg < '$dateNow'");
        });
    })
    ->count();

if ($cuotasConDeudaMoras > 0) {
    echo json_encode([
        'cuotas' => $cuotasConDeudaMoras,
        'message' => 'No podemos continuar por que el cliente tiene moras en sus otros créditos.',
        'success' => false
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

if ($credit->montoAprovado > $saldo) {
    echo json_encode([
        'saldo' => $saldo,
        'message' => 'No tienes suficiente saldo para desembolsar el credito.',
        'success' => false
    ]);
    die();
}

$installments = $credit->generateInstallments();

if (count($installments) === 0) {
    echo json_encode([
        'saldo' => $saldo,
        'message' => 'No se puede generar las cuotas.',
        'success' => false
    ]);
    die();
}

// actulizacion y creacion de la base de datos

$database::beginTransaction();

try {
    foreach ($installments as $installment) {
        $installment->save();
    }

    $numberCredit = Credit::where('idCG', $credit->idCG)
        ->where(function ($query) {
            $query->where('estado', 4)
                ->orWhere('estado', 5);
        })
        ->count();

    $credit->update([
        'estado' => 4,
        'fechaDesembolso' => $cash->ini,
        'n_credito' => $numberCredit + 1,
        'tofic' => $cash->idO
    ]);

    $transaction = new Transaction();
    $transaction->idCA = $cash->idCA;
    $transaction->tipo = 2;
    $transaction->total = $credit->montoAprovado;
    $transaction->cliente = $credit->idCG;
    $transaction->conejo = $credit->idP;
    $transaction->created_at = date('Y-m-d H:i:s');
    $transaction->save();

    $database::commit();
} catch (\Throwable $th) {
    $database::rollback();
    // echo $th->getMessage();
    echo json_encode([
        'saldo' => $saldo,
        'message' => 'Lo sentimos se produjo un error desconocido.',
        'success' => false
    ]);
    die();
}

echo json_encode([
    'saldo' => $saldo,
    'cuotas' => $installments,
    'success' => true,
    'message' => 'Credito desembolsado con exito!!'
]);
    }
}

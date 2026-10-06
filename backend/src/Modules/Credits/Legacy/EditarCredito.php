<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;
use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/editarCredito.php.
// El wrapper en app/api/editarCredito.php preserva URL, entradas y salida legacy.
class EditarCredito
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

$credit = Credit::where('idP', $request->creditId)
    ->where('estado', 4)
    ->first();

if (!$credit) {
    echo json_encode([
        'success' => false,
        'message' => 'El credito no puede ser editado.'
    ]);
    die();
}

$numberInstallments = Installment::where('idP', $credit->idP)
    ->where(function ($query) {
        $query->whereNotNull('estado')
            ->orWhereNotNull('estado_mora');
    })
    ->count();

if ($numberInstallments > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'El credito no puede ser editado.'
    ]);
    die();
}

$errors = $request->validate([
    'tasa' => 'required|numeric|min:1',
    'fechaDesembolso' => 'required|date',
    'prestamo' => 'required|numeric|digits_between:1,8',
    'tipoPago' => 'required|in:1,2,3,4,5',
    'fechaInicio' => 'nullable|date',
    'plazo' => 'required|numeric|min:1|max:60'
]);


if (count($errors) > 0) {
    echo json_encode([
        'success' => false,
        'errors' => $errors,
        'message' => 'Revise los datos ingresados.'
    ]);
    die();
}

$credit->pago = $request->tipoPago;
$credit->taza = $request->tasa;
$credit->montoAprovado = $request->prestamo;
$credit->fechaDesembolso = $request->fechaDesembolso;
$credit->started_at = $request->fechaInicio;
$credit->n_cuota = $request->plazo;
$credit->plazo = $request->plazo;

if ($request->prestamo <= 500) {
    $credit->mora = 0.5;
} else if ($request->prestamo > 500 && $request->prestamo <= 1000) {
    $credit->mora = 1;
} else {
    $credit->mora = 2;
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
    if ($credit->estado === "4") {
        Installment::where('idP', $credit->idP)->delete();
    }

    $credit->save();

    if ($credit->estado === "4") {
        foreach ($installments as $installment) {
            $installment->save();
        }
    }

    Transaction::where('tipo', 2)
        ->where('conejo', $credit->idP)
        ->update([
            'total' => $credit->montoAprovado
        ]);

    $database::commit();
} catch (\Throwable $th) {
    $database::rollback();
    // echo $th->getMessage();
    echo json_encode([
        'message' => 'Lo sentimos se produjo un error desconocido.',
        'success' => false
    ]);
    die();
}


echo json_encode([
    'success' => true,
    'message' => 'Credito editado con exito!!'
]);
    }
}

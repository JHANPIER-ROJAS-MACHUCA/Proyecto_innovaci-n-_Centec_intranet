<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/dataCredit.php.
// El wrapper en app/api/dataCredit.php preserva URL, entradas y salida legacy.
class DataCredit
{
    public static function handle(): void
    {$request = new Request();

$credit = Credit::with('installments')
    ->find($request->get('creditId'));


if (!$credit) {
    echo json_encode([
        'success' => false,
        'message' => 'El credito no existe'
    ]);
    die();
}



echo json_encode([
    'data' => $credit,
    'success' => true
]);
    }
}

<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/toggleRefinanciado.php.
// El wrapper en app/api/toggleRefinanciado.php preserva URL, entradas y salida legacy.
class ToggleRefinanciado
{
    public static function handle(): void
    {$request = new Request();

$credit = Credit::find($request->creditId);

if ($credit) {
    $credit->refinanced = !$credit->refinanced;
    $credit->save();
}
    }
}

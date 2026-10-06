<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/activarCredito.php.
// El wrapper en app/api/activarCredito.php preserva URL, entradas y salida legacy.
class ActivarCredito
{
    public static function handle(): void
    {$request = new Request();

Credit::where('idP', $request->creditId)
    ->where('estado', 5)
    ->update([
        'estado' => 4
    ]);

echo json_encode([
    'message' => 'Activado con exito.',
    'success' => true
]);
    }
}

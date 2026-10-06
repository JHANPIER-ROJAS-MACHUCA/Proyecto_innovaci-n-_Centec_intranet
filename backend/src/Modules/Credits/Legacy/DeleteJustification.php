<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/deleteJustification.php.
// El wrapper en app/api/deleteJustification.php preserva URL, entradas y salida legacy.
class DeleteJustification
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

$value = $database->table('non_payment_justifications')
->where('id', $request->nonPaymentJustificationId)
->delete();

echo json_encode([
    'success' => $value ? true : false,
    'message' => $value ? 'Eliminado con exito!!' : 'Lo sentimos, sucedio algo inesperado.'
]);
    }
}

<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/updateJustification.php.
// El wrapper en app/api/updateJustification.php preserva URL, entradas y salida legacy.
class UpdateJustification
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

if (!$request->description) {
    echo json_encode([
        'success' => false,
        'message' => 'La description no puede estar vacia.'
    ]);
    die();
}

$value = $database->table('non_payment_justifications')
    ->where('id', $request->id)
    ->update([
        'description' => $request->description
    ]);

echo json_encode([
    'success' => true,
    'message' => 'Actualizado con exito!!'
]);
    }
}

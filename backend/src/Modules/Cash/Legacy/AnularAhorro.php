<?php

namespace CrediSoporte\Modules\Cash\Legacy;

use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/anularAhorro.php.
// El wrapper en app/api/anularAhorro.php preserva URL, entradas y salida legacy.
class AnularAhorro
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

$deleteAhorro = $database->table('tahorro_deta')
    ->where('idAd', $request->ahorroTransactionId)
    ->update(['estad' => '0']);

$deleteTransaction = $database->table('tcaja_usu_detal')
    ->where('idCuota', $request->ahorroTransactionId)
    ->update(['estadodt' => '1']);

echo json_encode([
    'message' => 'Eliminado con exito!!',
    'success' => true
]);
    }
}

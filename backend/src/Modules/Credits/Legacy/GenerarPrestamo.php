<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/generarPrestamo.php.
// El wrapper en app/api/generarPrestamo.php preserva URL, entradas y salida legacy.
class GenerarPrestamo
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

$identificador = $request->get('identi');

// $spouseId = null;
// if (strlen($request->post('txtdni')) === 8) {
//     $spouseId = Customer::updateOrCreate([
//         ['dni' => $request->post('txtdni')],
//         [
//             'ap' => $request->post('txtap'),
//             'am' => $request->post('txtam'),
//             'nom' => $request->post('txtnom'),
//             'sexo' => $request->post('sexo')
//         ]
//     ]);
// }

// $avalId = null;
// if (strlen($request->post('txtdni1')) === 8) {
//     $avalId = Customer::updateOrCreate([
//         ['dni' => $request->post('txtdni1')],
//         [
//             'ap' => $request->post('txtap1'),
//             'am' => $request->post('txtam1'),
//             'nom' => $request->post('txtnom1'),
//             'direc' => $request->post('txtdirec'),
//         ]
//     ]);
// }

// intervalo de pagos
$diasPasados = "0";

switch ($request->post('txtpago')) {
    case 1:
        $diasPasados = "1";
        break;
    case 2:
        $diasPasados = "7";
        break;
    case 3:
        $diasPasados = "1";
        break;
    case 4:
        $diasPasados = "30";
        break;
}

$spouse = Customer::where('dni', $request->post('txtdni'))->first(['idCG']);
$aval = Customer::where('dni', $request->post('txtdni1'))->first(['idCG']);

$tvinculacionId = $database::table('tvinculacion')
    ->insertGetId([
        'titular' => $identificador,
        'conyugue' => !is_null($spouse) ? $spouse->idCG : null,
        'aval' => !is_null($aval) ? $aval->idCG : null
    ]);

Credit::create([
    'idCG' => $identificador,
    'idV' => $tvinculacionId,
    'montoPropuesto' => $request->post('txtmonto'),
    'cuota' => $request->post('txtcuotaf1'),
    'taza' => $request->post('txtinteres'),
    'pago' => $request->post('txtpago'),
    'plazo' => $request->post('txtplazo'),
    'n_cuota' => $request->post('txtplazo'),
    'diasPasados' => $diasPasados,
    'estado' => 1,
    'tipoP' => $request->post('txttipoPresta'),
    'mora' => $request->post('txtmora'),
    'user_id' => $request->post('user_id', null),
    'started_at' => $request->post('started_at', null)
]);
    }
}

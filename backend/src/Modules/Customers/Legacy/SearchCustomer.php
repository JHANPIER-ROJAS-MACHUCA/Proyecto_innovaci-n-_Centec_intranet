<?php

namespace CrediSoporte\Modules\Customers\Legacy;

use CrediSoporte\Domain\Request\Request;
use CrediSoporte\Domain\Models\Customer;

// Logica migrada verbatim desde app/api/searchCustomer.php.
// El wrapper en app/api/searchCustomer.php preserva URL, entradas y salida legacy.
class SearchCustomer
{
    public static function handle(): void
    {$request = new Request();

// validando que el dni ingresado sea 8 digotos
if (strlen($request->get('dni')) !== 8) {
    echo json_encode([
        'message' => 'El dni debe contener 8 digitos',
        'success' => false
    ]);
    die();
}

$dni = $request->get('dni');
$customer = Customer::where('dni', $dni)->first();

if (!is_null($customer)) {
    echo json_encode([
        'api_origin' => 'recurrent',
        'data' => [
            'document' => $customer->dni,
            'name' => $customer->nom,
            'surname' => $customer->ap . ' ' . $customer->am,
            'direction' => $customer->direc,
            'cell_phone' => $customer->cel,
            'client_type' => $customer->client_type,
            'risk_profile_id' => $customer->risk_profile_id,
        ],
        'success' => true
    ]);
    die();
}

$token = $_ENV['APISPERU_TOKEN'] ?? '';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://dniruc.apisperu.com/api/v1/dni/$dni?token=$token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
    die();
}

$objeto = json_decode($response);

if (empty($objeto)) {
    echo json_encode([
        'message' => 'No se encontraron resultados',
        'success' => false
    ]);
    die();
}

echo json_encode([
    'api_origin' => 'new',
    'data' => [
        'document' => $objeto->dni,
        'name' => $objeto->nombres,
        'surname' => $objeto->apellidoPaterno . ' ' . $objeto->apellidoMaterno
    ],
    'success' => true
]);
    }
}

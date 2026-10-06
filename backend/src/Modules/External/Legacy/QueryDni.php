<?php

namespace CrediSoporte\Modules\External\Legacy;


// Logica migrada verbatim desde app/api/queryDni.php.
// El wrapper en app/api/queryDni.php preserva URL, entradas y salida legacy.
class QueryDni
{
    public static function handle(): void
    {// Datos
$token = $_ENV['APISNET_TOKEN'] ?? '';
$dni = $_GET['number'];

// Iniciar llamada a API
$curl = curl_init();

// Buscar dni
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.apis.net.pe/v1/dni?numero=' . $dni,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 2,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Referer: https://apis.net.pe/consulta-dni-api',
    'Authorization: Bearer ' . $token
  ),
));

$response = curl_exec($curl);

curl_close($curl);
// Datos listos para usar

$response = json_decode($response);

if (isset($response->error) && !empty($response->error)) {
    echo json_encode([
        'message' => $response->error,
        'success' => false
    ]);
    die();
}

echo json_encode([
    'data' => [
        'document' => $response->numeroDocumento,
        'name' => $response->nombres,
        'father_first_surname' => $response->apellidoPaterno,
        'mother_first_surname' => $response->apellidoMaterno,
    ],
    'success' => true
]);
    }
}

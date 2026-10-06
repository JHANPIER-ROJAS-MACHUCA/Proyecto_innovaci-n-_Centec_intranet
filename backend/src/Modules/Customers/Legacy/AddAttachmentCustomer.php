<?php

namespace CrediSoporte\Modules\Customers\Legacy;

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/addAttachmentCustomer.php.
// El wrapper en app/api/addAttachmentCustomer.php preserva URL, entradas y salida legacy.
class AddAttachmentCustomer
{
    public static function handle(): void
    {$dotenv = Dotenv\Dotenv::createImmutable(\CrediSoporte\Core\Bootstrap::root());
$dotenv->load();

$request = new Request();

$customer = Customer::find($request->customer_id);

if (!$customer) {
    echo json_encode([
        'message' => 'El cliente no existe.',
        'success' => false
    ]);
    die();
}

$currentLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/uploads/' . $request->name;
$newLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/customers/' . $request->name;

if (!rename($currentLocation, $newLocation)) {
    echo json_encode([
        'message' => 'Lo sentimos, no se pudo subir la imagen.',
        'success' => false
    ]);
    die();
}

$data = $customer->attachments()->create([
    'extension' => $request->extension,
    'path' => $_ENV['APP_URL'] . '/storage/customers/',
    'name' => $request->name
]);

echo json_encode([
    'customer' => $customer,
    'data' => $data,
    'message' => 'Imagen subido con exito!!',
    'attachments' => $customer->attachments,
    'success' => true,
]);
    }
}

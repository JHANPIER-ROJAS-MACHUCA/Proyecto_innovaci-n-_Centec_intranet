<?php

namespace CrediSoporte\Modules\Customers\Legacy;

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\CustomerAttachment;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/deleteCustomerAttachment.php.
// El wrapper en app/api/deleteCustomerAttachment.php preserva URL, entradas y salida legacy.
class DeleteCustomerAttachment
{
    public static function handle(): void
    {$request = new Request();

$attachment = CustomerAttachment::find($request->attachment_id);

if (!$attachment) {
    echo json_encode([
        'message' => 'La imagen no existe',
        'success' => false
    ]);
    die();
}

unlink(\CrediSoporte\Core\Bootstrap::root() . '/storage/customers/' . $attachment->name);

$attachment->delete();

echo json_encode([
    'success' => true,
    'message' => 'Imagen eliminado con exito!!',
    'attachments' => Customer::find($attachment->customer_id)->attachments
]);
    }
}

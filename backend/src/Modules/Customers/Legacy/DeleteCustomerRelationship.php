<?php

namespace CrediSoporte\Modules\Customers\Legacy;

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\Relation;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/deleteCustomerRelationship.php.
// El wrapper en app/api/deleteCustomerRelationship.php preserva URL, entradas y salida legacy.
class DeleteCustomerRelationship
{
    public static function handle(): void
    {$request = new Request();

$customer = Customer::find($request->customer_id);

if (!$customer) {
    http_response_code(400);
    die();
}

$customer->relations()->detach($request->relation_id);

echo json_encode([
    "success" => true,
    "relations" => $customer->relations
]);
    }
}

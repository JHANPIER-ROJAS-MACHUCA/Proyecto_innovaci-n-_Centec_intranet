<?php

namespace CrediSoporte\Modules\Customers\Legacy;

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\Relation;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/addCustomerRelationship.php.
// El wrapper en app/api/addCustomerRelationship.php preserva URL, entradas y salida legacy.
class AddCustomerRelationship
{
    public static function handle(): void
    {$request = new Request();

$customer = Customer::find($request->customer_id);

if (!$customer) {
    http_response_code(400);
    die();
}

$customerRelation = Customer::where('idCG', $request->relation_id)->first();

$errors = $request->validate([
    'relation_id' => 'required|exists',
    'type' => 'in:spouse,endorsement'
], [
    'relation_id.required' => 'Este campo es requerido.',
    'relation_id.exists' => 'El valor del campo no es válido.',
    'type.in' => 'El valor seleccionado no es válido.'
], [
    'relation_id.exists' => $customerRelation !== null
]);

if (count($errors)) {
    echo json_encode([
        'errors' => $errors,
        'success' => false
    ]);
    die();
}

$relation = $customer->relations()
    ->attach([$request->relation_id => $request->only(['type'])]);

echo json_encode([
    "success" => true,
    "relations" => $customer->relations
]);
    }
}

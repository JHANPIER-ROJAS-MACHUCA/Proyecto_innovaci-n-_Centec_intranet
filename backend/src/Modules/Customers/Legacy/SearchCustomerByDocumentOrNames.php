<?php

namespace CrediSoporte\Modules\Customers\Legacy;

use CrediSoporte\Domain\Request\Request;
use CrediSoporte\Domain\Models\Customer;

// Logica migrada verbatim desde app/api/searchCustomerByDocumentOrNames.php.
// El wrapper en app/api/searchCustomerByDocumentOrNames.php preserva URL, entradas y salida legacy.
class SearchCustomerByDocumentOrNames
{
    public static function handle(): void
    {$request = new Request();

$search = (string) $request->search;

$customers = Customer::whereRaw("concat_ws(' ',ap, am, nom) like ?", ['%' . $search . '%'])
    ->orWhere('dni', 'like', $search . '%')
    ->limit(20)
    ->get();

echo json_encode($customers);
    }
}

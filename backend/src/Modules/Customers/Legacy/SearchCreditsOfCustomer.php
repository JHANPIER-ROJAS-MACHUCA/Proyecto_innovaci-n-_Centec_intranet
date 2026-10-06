<?php

namespace CrediSoporte\Modules\Customers\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/searchCreditsOfCustomer.php.
// El wrapper en app/api/searchCreditsOfCustomer.php preserva URL, entradas y salida legacy.
class SearchCreditsOfCustomer
{
    public static function handle(): void
    {$request = new Request();

$credits = Credit::where('idCG', $request->get('customerId'))
->where('estado', 4)->get();

echo json_encode($credits);
    }
}

<?php

namespace CrediSoporte\Modules\Goals\Legacy;

use CrediSoporte\Domain\Models\Goal;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/deleteMeta.php.
// El wrapper en app/api/deleteMeta.php preserva URL, entradas y salida legacy.
class DeleteMeta
{
    public static function handle(): void
    {$request = new  Request();

$f = Goal::where('id', $request->goalId)->delete();

echo json_encode([
    'affectadas' => $f,
    'success' => true
]);
    }
}

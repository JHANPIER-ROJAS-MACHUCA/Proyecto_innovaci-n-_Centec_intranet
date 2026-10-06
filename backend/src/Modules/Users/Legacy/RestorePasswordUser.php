<?php

namespace CrediSoporte\Modules\Users\Legacy;

use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/restorePasswordUser.php.
// El wrapper en app/api/restorePasswordUser.php preserva URL, entradas y salida legacy.
class RestorePasswordUser
{
    public static function handle(): void
    {$request = new Request();

$user = User::find($request->user_id);

if (!$user) {
    echo json_encode([
        'message' => 'Usuario no existe',
        'success' => false
    ]);
    die();
}

$user->pass = md5('123456');
$user->save();

echo json_encode([
    'success' => true,
    'message' => 'Contraseña restablecida a "123456"'
]);
    }
}

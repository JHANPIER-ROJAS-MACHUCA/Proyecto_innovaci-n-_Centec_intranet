<?php

namespace CrediSoporte\Modules\Users\Legacy;

use CrediSoporte\Domain\Models\Goal;
use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/deleteUserAvatar.php.
// El wrapper en app/api/deleteUserAvatar.php preserva URL, entradas y salida legacy.
class DeleteUserAvatar
{
    public static function handle(): void
    {$request = new  Request();

$user = User::find($request->user_id);

if (!$user) {
    echo json_encode([
        'message' => 'El usuario no existe.',
        'success' => false
    ]);
    die();
}

unlink(\CrediSoporte\Core\Bootstrap::root() . "/storage/profiles/$user->img");

$user->img = null;
$user->save();

echo json_encode([
    'message' => 'Foto de perfil eliminado con éxito!!',
    'success' => true
]);
    }
}

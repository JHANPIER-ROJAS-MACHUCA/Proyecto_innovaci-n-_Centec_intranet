<?php

namespace CrediSoporte\Modules\Users\Legacy;

use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/toggleEstadoUsuario.php.
// El wrapper en app/api/toggleEstadoUsuario.php preserva URL, entradas y salida legacy.
class ToggleEstadoUsuario
{
    public static function handle(): void
    {$request = new Request();

$user = User::find($request->get('user_id'));

if (!$user) {
    echo json_encode([
        'message' => 'Es usuario no existe.',
        'success' => false
    ]);
    die();
}

$user->estadoU = $user->estadoU == '1' ? '2' : '1';
$user->save();

echo json_encode([
    'estado' => $user->estadoU,
    'message' => $user->estadoU == 2 ? 'Usuario eliminado con exito!!' : 'Usuario habilitado con exito!!',
    'success' => true
]);
    }
}

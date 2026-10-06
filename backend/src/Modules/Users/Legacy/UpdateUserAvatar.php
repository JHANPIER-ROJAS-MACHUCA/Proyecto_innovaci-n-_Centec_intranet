<?php

namespace CrediSoporte\Modules\Users\Legacy;

use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/updateUserAvatar.php.
// El wrapper en app/api/updateUserAvatar.php preserva URL, entradas y salida legacy.
class UpdateUserAvatar
{
    public static function handle(): void
    {$request = new Request();

$user = User::find($request->user_id);

if (!$user) {
    echo json_encode([
        'message' => 'El usuario no existe.',
        'success' => false
    ]);
    die();
}

$currentLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/uploads/' . $request->name;
$newLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/profiles/' . $request->name;

if (!rename($currentLocation, $newLocation)) {
    echo json_encode([
        'message' => 'Lo sentimos, no se pudo actualizar tu foto de perfil.',
        'success' => false
    ]);
    die();
}

$user->img = $request->name;
$user->save();

echo json_encode([
    'user' => $user,
    'message' => 'Foto de perfil actualizado.',
    'success' => true
]);
    }
}

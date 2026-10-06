<?php

namespace CrediSoporte\Modules\Auth\Legacy;

use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/login.php.
// El wrapper en app/api/login.php preserva URL, entradas y salida legacy.
class Login
{
    public static function handle(): void
    {$request = new Request();

$pas = md5($request->pas);

$user = User::where('dniU', $request->usu)
    ->where('pass', $pas)
    ->where('estadoU', 1)
    ->first();

if (is_null($user)) {
    echo 0;
    die();
}

setcookie("user1", $user['idU'], time() + 60 * 60 * 24, "/", "");
setcookie("nombre_U", $user['apU'] . ' ' . $user['amU'] . ' ' . $user['nomU'], time() + 60 * 60 * 24, "/", "");
setcookie("tuser", $user['tipoU'], time() + 60 * 60 * 24, "/", "");
setcookie("tofi", $user['idO'], time() + 60 * 60 * 24, "/", "");

echo 1;
    }
}

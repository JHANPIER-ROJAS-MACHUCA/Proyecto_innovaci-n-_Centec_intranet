<?php

namespace CrediSoporte\Modules\Auth\Legacy;


// Logica migrada verbatim desde app/api/logout.php.
// El wrapper en app/api/logout.php preserva URL, entradas y salida legacy.
class Logout
{
    public static function handle(): void
    {setcookie('user1', '', time() - 100, "/");
setcookie('nombre_U', '', time() - 100, "/");
setcookie('tuser', '', time() - 100, "/");
setcookie('tofi', '', time() - 100, "/");
setcookie('arcaneo', '', time() - 100, "/");


echo 1;
    }
}

<?php

namespace CrediSoporte\Modules\Auth;

use CrediSoporte\Domain\Models\User;

// Autenticacion contra tusuario (misma regla que app/api/login.php).
class AuthService
{
    public function attempt(string $dni, string $plainPassword): ?User
    {
        return User::where('dniU', $dni)
            ->where('pass', md5($plainPassword))
            ->where('estadoU', 1)
            ->first();
    }
}

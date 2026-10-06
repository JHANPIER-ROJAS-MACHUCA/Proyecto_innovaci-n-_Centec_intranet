<?php

namespace CrediSoporte\Domain\Session;

// Flash de sesion compatible con el uso legacy ($session->setFlash()).
// Estructura: $_SESSION['flash'][clave] (la misma que leia Request::old()).
class Session
{
    public function setFlash(string $key, $value): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['flash'][$key] = $value;
    }

    public function getFlash(string $key, $default = null)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
}

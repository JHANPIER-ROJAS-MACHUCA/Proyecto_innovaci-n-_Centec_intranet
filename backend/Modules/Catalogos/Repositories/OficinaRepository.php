<?php
// Módulo Catalogos — repositorio de oficinas (extraído de
// repositories/CatalogoRepository.php, phase2-modular; idéntico).

class OficinaRepository
{
    public static function listar()
    {
        global $capsule;
        return $capsule->table('toficina')->get();
    }
}

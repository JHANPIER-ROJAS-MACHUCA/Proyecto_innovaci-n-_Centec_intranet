<?php
// Módulo Catalogos — servicio de empresa (extraído de services/CatalogoService.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/EmpresaRepository.php';

class EmpresaService
{
    public static function ver(): ?object
    {
        return EmpresaRepository::ver();
    }

    public static function actualizar(array $in): void
    {
        EmpresaRepository::actualizar($in);
    }
}

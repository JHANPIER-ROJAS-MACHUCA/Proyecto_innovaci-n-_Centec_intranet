<?php
// Módulo Catalogos — servicio de oficinas (extraído de services/CatalogoService.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/OficinaRepository.php';

class OficinaService
{
    public static function listar()
    {
        return OficinaRepository::listar();
    }
}

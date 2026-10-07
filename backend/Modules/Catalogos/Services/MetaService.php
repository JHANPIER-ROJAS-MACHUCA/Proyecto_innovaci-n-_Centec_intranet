<?php
// Módulo Catalogos — servicio de metas (extraído de services/CatalogoService.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/MetaRepository.php';

class MetaService
{
    public static function listar()
    {
        return MetaRepository::listar();
    }

    public static function porUsuario()
    {
        return MetaRepository::porUsuario();
    }

    public static function crear(array $in)
    {
        return MetaRepository::crear($in)->toArray();
    }

    public static function actualizar(int $id, array $in)
    {
        return MetaRepository::actualizar($id, $in);
    }

    public static function eliminar(int $id): void
    {
        MetaRepository::eliminar($id);
    }
}

<?php
// Módulo Catalogos — servicio de justificaciones (extraído de
// services/CatalogoService.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/JustificacionRepository.php';

class JustificacionService
{
    public static function listar(?int $creditId)
    {
        return JustificacionRepository::listar($creditId);
    }

    public static function crear(array $in)
    {
        return JustificacionRepository::crear($in)->toArray();
    }

    public static function actualizar(int $id, array $in)
    {
        return JustificacionRepository::actualizar($id, $in);
    }

    public static function eliminar(int $id): void
    {
        JustificacionRepository::eliminar($id);
    }
}

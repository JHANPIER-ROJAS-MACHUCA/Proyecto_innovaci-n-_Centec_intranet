<?php
// Módulo Propuestas — servicio (extraído de services/CatalogoService.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/PropuestaRepository.php';

class PropuestaService
{
    public static function listar()
    {
        return PropuestaRepository::listar();
    }

    public static function detalle(int $id): ?object
    {
        return PropuestaRepository::detalle($id);
    }

    public static function crear(array $in, int $userId)
    {
        return PropuestaRepository::crear($in + ['user_id' => $userId])->toArray();
    }

    public static function evaluar(int $id, array $in): void
    {
        if (!$id) throw new DomainException('id requerido.');
        PropuestaRepository::evaluar($id, $in);
    }

    public static function eliminar(int $id): void
    {
        PropuestaRepository::eliminar($id);
    }

    public static function responder(array $in)
    {
        if (empty($in['proposal_id'])) throw new DomainException('proposal_id requerido.');
        return PropuestaRepository::responder($in)->toArray();
    }
}

<?php
// Módulo Catalogos — servicio de adjuntos (extraído de services/CatalogoService.php,
// phase2-modular; idéntico, incluida la ruta de storage corregida a raíz backend).
require_once __DIR__ . '/../Repositories/AdjuntoRepository.php';

class AdjuntoService
{
    public static function listar(int $customerId)
    {
        return AdjuntoRepository::listar($customerId);
    }

    public static function crear(array $in)
    {
        return AdjuntoRepository::crear($in)->toArray();
    }

    public static function eliminar(int $id): void
    {
        AdjuntoRepository::eliminar($id);
    }

    public static function guardarArchivo(array $file): string
    {
        $name = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', basename($file['name']));
        $dest = dirname(__DIR__, 3) . '/storage/uploads/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) throw new DomainException('No se pudo guardar.');
        return $name;
    }
}

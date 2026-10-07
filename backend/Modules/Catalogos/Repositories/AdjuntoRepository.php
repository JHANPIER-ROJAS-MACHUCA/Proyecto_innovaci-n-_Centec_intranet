<?php
// Módulo Catalogos — repositorio de adjuntos (extraído de
// repositories/CatalogoRepository.php, phase2-modular; idéntico).

class AdjuntoRepository
{
    public static function listar(int $customerId)
    {
        return \App\Models\CustomerAttachment::where('customer_id', $customerId)->get();
    }

    public static function crear(array $data)
    {
        return \App\Models\CustomerAttachment::create($data);
    }

    public static function eliminar(int $id): void
    {
        \App\Models\CustomerAttachment::where('id', $id)->delete();
    }
}

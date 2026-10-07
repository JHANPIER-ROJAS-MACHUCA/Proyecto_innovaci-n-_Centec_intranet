<?php
// Módulo Catalogos — repositorio de justificaciones (extraído de
// repositories/CatalogoRepository.php, phase2-modular; idéntico).

class JustificacionRepository
{
    public static function listar(?int $creditId)
    {
        $q = \App\Models\Comment::orderBy('id', 'desc')->limit(100);
        if ($creditId) $q->where('credit_id', $creditId);
        return $q->get();
    }

    public static function crear(array $data)
    {
        return \App\Models\Comment::create($data);
    }

    public static function actualizar(int $id, array $data)
    {
        return \App\Models\Comment::where('id', $id)->update($data);
    }

    public static function eliminar(int $id): void
    {
        \App\Models\Comment::where('id', $id)->delete();
    }
}

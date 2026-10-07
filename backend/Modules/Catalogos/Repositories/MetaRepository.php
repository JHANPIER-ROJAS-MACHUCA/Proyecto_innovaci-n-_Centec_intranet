<?php
// Módulo Catalogos — repositorio de metas (extraído de
// repositories/CatalogoRepository.php, phase2-modular; idéntico).

class MetaRepository
{
    public static function listar()
    {
        return \App\Models\Goal::orderBy('id', 'desc')->limit(100)->get();
    }

    public static function porUsuario()
    {
        global $capsule;
        return $capsule->table('goals as g')->join('tusuarios as u', 'g.user_id', 'u.idU')
            ->leftJoin('tdatosu as d', 'd.idU', 'u.idU')
            ->select('g.*', 'd.dniU')->orderBy('g.id', 'desc')->limit(100)->get();
    }

    public static function crear(array $data)
    {
        return \App\Models\Goal::create($data);
    }

    public static function actualizar(int $id, array $data)
    {
        return \App\Models\Goal::where('id', $id)->update($data);
    }

    public static function eliminar(int $id): void
    {
        \App\Models\Goal::where('id', $id)->delete();
    }
}

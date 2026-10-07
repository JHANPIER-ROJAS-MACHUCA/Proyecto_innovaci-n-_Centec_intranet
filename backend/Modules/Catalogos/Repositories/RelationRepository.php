<?php
// Módulo Catalogos — repositorio de vinculaciones (extraído de
// repositories/CatalogoRepository.php, phase2-modular; idéntico).

class RelationRepository
{
    public static function listar(int $idCG)
    {
        return \App\Models\Relation::where('customer_id', $idCG)->get();
    }

    public static function vinculacionCompleta($idV): ?object
    {
        global $capsule;
        return $capsule->table('tvinculacion')
            ->leftJoin('tclie_general as aval', 'tvinculacion.aval', 'aval.idCG')
            ->leftJoin('tclie_general as conyuge', 'tvinculacion.conyugue', 'conyuge.idCG')
            ->where('idV', $idV)
            ->select(
                'conyuge.ap as conyuge_ap',
                'conyuge.am as conyuge_am',
                'conyuge.nom as conyuge_nom',
                'conyuge.dni as conyuge_dni',
                'aval.ap as aval_ap',
                'aval.am as aval_am',
                'aval.nom as aval_nom',
                'aval.dni as aval_dni'
            )->first();
    }

    public static function crear(array $data)
    {
        return \App\Models\Relation::create($data);
    }

    public static function eliminar(int $id): void
    {
        \App\Models\Relation::where('id', $id)->delete();
    }
}

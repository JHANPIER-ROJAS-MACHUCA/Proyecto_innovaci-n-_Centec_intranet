<?php
// Acceso a rbac_roles / rbac_permisos / rbac_rol_permiso.

class RbacRepository
{
    public static function roles(): array
    {
        global $capsule;
        return $capsule->table('rbac_roles')->orderBy('nivel_operativo')->orderBy('codigo')
            ->get()->map(fn($r) => (array) $r)->all();
    }

    public static function permisos(?string $modulo = null): array
    {
        global $capsule;
        $q = $capsule->table('rbac_permisos')->orderBy('modulo')->orderBy('vista')->orderBy('accion');
        if ($modulo) $q->where('modulo', $modulo);
        return $q->get()->map(fn($r) => (array) $r)->all();
    }

    public static function asignaciones(?int $idRol = null): array
    {
        global $capsule;
        $q = $capsule->table('rbac_rol_permiso as rp')
            ->join('rbac_permisos as p', 'p.id', 'rp.idPermiso')
            ->select('rp.idRol', 'rp.idPermiso', 'rp.estado', 'p.modulo', 'p.vista', 'p.accion');
        if ($idRol !== null) $q->where('rp.idRol', $idRol);
        return $q->get()->map(fn($r) => (array) $r)->all();
    }

    public static function mapaRol(int $idRol): array
    {
        $map = [];
        foreach (self::asignaciones($idRol) as $a) {
            if ((int) ($a['estado'] ?? 0) !== 1) continue;
            $map[$a['modulo'] . '.' . $a['vista'] . '.' . $a['accion']] = true;
        }
        return $map;
    }

    public static function reemplazarAsignaciones(int $idRol, array $idsPermiso): void
    {
        global $capsule;
        $capsule->table('rbac_rol_permiso')->where('idRol', $idRol)->delete();
        foreach (array_unique(array_map('intval', $idsPermiso)) as $id) {
            if ($id > 0) $capsule->table('rbac_rol_permiso')->insert(['idRol' => $idRol, 'idPermiso' => $id, 'estado' => 1]);
        }
    }

    public static function setEstado(int $idPermiso, int $estado): void
    {
        \App\Models\RbacPermiso::where('id', $idPermiso)->update(['estado' => $estado ? 1 : 0]);
    }
}

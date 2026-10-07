<?php
// NOTA phase2: ReporteRepository migrÃ³ a Modules/Reportes/Repositories/.


// NOTA phase2: Oficina/Empresa/Relation/Adjunto/Justificacion/Meta migraron a Modules/Catalogos/. Aquí queda CpanelRepository (dashboard agregado).

class CpanelRepository
{
    public static function conteos(): array
    {
        global $capsule;
        return [
            'clientes' => (int) $capsule->table('tclie_general')->count(),
            'creditosActivos' => (int) $capsule->table('tprestamo')->where('estado', 4)->count(),
            'creditosPropuestos' => (int) $capsule->table('tprestamo')->where('estado', 1)->count(),
            'morasPendientes' => (int) $capsule->table('tpresta_detalle as d')
                ->join('tprestamo as p', 'd.idP', 'p.idP')
                ->where('p.estado', 4)->where('d.estado', '!=', 1)->where('d.fechaProg', '<', date('Y-m-d'))->count(),
            'extornosPendientes' => (int) $capsule->table('textorno')->count(),
            'billetajePendiente' => (int) $capsule->table('tbilletaje')->where('estado', '2')->count(),
            'usuariosActivos' => (int) $capsule->table('tusuarios')->where('idEstado', 1)->count(),
            'oficinas' => (int) $capsule->table('toficina')->count(),
            'metas' => (int) $capsule->table('goals')->count(),
        ];
    }

    public static function carteraPropia($idU): array
    {
        global $capsule;
        return [
            'misClientes' => (int) $capsule->table('tclie_general')->where('idU', $idU)->count(),
            'misCreditos' => (int) $capsule->table('tprestamo as p')
                ->join('tclie_general as c', 'p.idCG', 'c.idCG')
                ->where('p.estado', 4)->where('c.idU', $idU)->count(),
            'misCobrosHoy' => (int) $capsule->table('tpresta_detalle as d')
                ->join('tprestamo as p', 'd.idP', 'p.idP')
                ->join('tclie_general as c', 'p.idCG', 'c.idCG')
                ->where('p.estado', 4)->where('d.expiration_at', date('Y-m-d'))->where('c.idU', $idU)->count(),
        ];
    }

    public static function cobrosHoyEquipo(): int
    {
        global $capsule;
        return (int) $capsule->table('tpresta_detalle as d')
            ->join('tprestamo as p', 'd.idP', 'p.idP')
            ->where('p.estado', 4)->where('d.expiration_at', date('Y-m-d'))->count();
    }
}

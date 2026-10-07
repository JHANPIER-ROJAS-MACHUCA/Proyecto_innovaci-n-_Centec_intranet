<?php
// Módulo Reportes — repositorio (extraído de repositories/CatalogoRepository.php,
// phase2-modular; idéntico).

class ReporteRepository
{
    public static function sentinel(int $limit = 200)
    {
        global $capsule;
        return $capsule->table('tprestamo as p')
            ->join('tclie_general as c', 'p.idCG', 'c.idCG')
            ->leftJoin('relations as r', 'r.customer_id', 'c.idCG')
            ->where('p.estado', 4)
            ->select('p.idP', 'p.montoPropuesto', 'c.dni', 'c.ap', 'c.nom')
            ->selectRaw('count(r.id) as relaciones')
            ->groupBy('p.idP')->orderBy('p.idP', 'desc')->limit($limit)->get();
    }

    public static function cancelados(int $limit = 200)
    {
        global $capsule;
        return $capsule->table('tprestamo as p')
            ->join('tclie_general as c', 'p.idCG', 'c.idCG')
            ->whereIn('p.estado', [5, 6])->orderBy('p.idP', 'desc')->limit($limit)
            ->select('p.idP', 'p.montoPropuesto', 'p.estado', 'c.dni', 'c.ap', 'c.nom')->get();
    }

    public static function sinCreditos(int $limit = 200)
    {
        global $capsule;
        return $capsule->table('tclie_general as c')
            ->leftJoin('tprestamo as p', 'p.idCG', 'c.idCG')
            ->whereNull('p.idP')->select('c.idCG', 'c.dni', 'c.ap', 'c.nom', 'c.cel')
            ->limit($limit)->get();
    }

    public static function vinculaciones(?string $titular, int $limit = 100)
    {
        global $capsule;
        $q = $capsule->table('tvinculacion')->orderBy('idV', 'desc')->limit($limit);
        if ($titular) $q->where('titular', $titular);
        return $q->get();
    }

    public static function proyecciones(string $desde, string $hasta): array
    {
        $desembolso = \App\Models\Credit::selectRaw('sum(montoAprovado) as totalDesembolso')
            ->whereIn('estado', [4, 5])->whereBetween('fechaDesembolso', [$desde, $hasta])->first();
        $cobros = \App\Models\Transaction::selectRaw('sum(total) as total')
            ->selectRaw('sum(cuota) as cuota')->selectRaw('sum(mora) as mora')
            ->where('estadodt', 2)->where('tipo', 3)->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->first();
        $proy = \App\Models\Installment::selectRaw('sum(tpresta_detalle.cuota) as cuota')
            ->selectRaw('sum(tpresta_detalle.interest) as interes')
            ->join('tprestamo', 'tpresta_detalle.idP', 'tprestamo.idP')
            ->whereIn('tprestamo.estado', [4, 5])->whereBetween('tpresta_detalle.fechaProg', [$desde, $hasta])->first();
        return ['desembolso' => $desembolso, 'cobros' => $cobros, 'proyeccion' => $proy];
    }
}

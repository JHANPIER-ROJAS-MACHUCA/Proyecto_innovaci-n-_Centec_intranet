<?php
// Módulo Creditos — repositorio de moras (extraído de
// repositories/FinancieraRepository.php, phase2-modular; idéntico).

class MoraRepository
{
    public static function deudores(int $limit = 100)
    {
        global $capsule;
        return $capsule->table('tpresta_detalle as d')
            ->join('tprestamo as p', 'd.idP', 'p.idP')
            ->join('tclie_general as c', 'p.idCG', 'c.idCG')
            ->where('p.estado', 4)->where('d.estado', '!=', 1)->where('d.fechaProg', '<', date('Y-m-d'))
            ->select('c.idCG', 'c.dni', 'c.ap', 'c.am', 'c.nom', 'p.idP', 'd.fechaProg', 'd.cuota', 'd.montoPagado')
            ->orderBy('d.fechaProg')->limit($limit)->get();
    }

    public static function morasPorDias(int $limit = 200)
    {
        global $capsule;
        return $capsule->table('tpresta_detalle as d')
            ->join('tprestamo as p', 'd.idP', 'p.idP')
            ->join('tclie_general as c', 'p.idCG', 'c.idCG')
            ->where('p.estado', 4)->where('d.estado', '!=', 1)->where('d.fechaProg', '<', date('Y-m-d'))
            ->select('c.dni', 'c.ap', 'c.nom', 'p.idP', 'd.fechaProg')
            ->selectRaw('DATEDIFF(CURDATE(), d.fechaProg) as dias, round(d.cuota - d.montoPagado, 2) as deuda')
            ->orderBy('dias', 'desc')->limit($limit)->get();
    }

    public static function condonar(int $creditId, string $date): void
    {
        global $capsule;
        $capsule->table('condone_dates')->insert(['credit_id' => $creditId, 'date' => $date]);
    }

    public static function condonarTodas(int $creditId, array $dates): void
    {
        global $capsule;
        foreach ($dates as $d) {
            $capsule->table('condone_dates')->insert(['credit_id' => $creditId, 'date' => $d]);
        }
    }

    public static function limpiarHuerfanas(): int
    {
        global $capsule;
        return $capsule->table('tpresta_detalle')->whereNotNull('pagoMora')->whereNull('tfechaMora')->update(['pagoMora' => null]);
    }
}

<?php
// Spec 2026 #4: avance de cobros diarios consultable por día + snapshot.
// Fuente: tcaja_usu_detal (tipo=3 cobros, estadodt=2 válidos) vía tcaja_usuario.

class AvanceService
{
    public static function delDia(string $fecha, ?int $idU = null): array
    {
        global $capsule;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) throw new DomainException('Fecha inválida (YYYY-MM-DD).');
        $q = $capsule->table('tcaja_usu_detal as t')->join('tcaja_usuario as cu', 't.idCA', 'cu.idCA')
            ->leftJoin('tusuarios as u', 'u.idU', 'cu.idU')
            ->leftJoin('tdatosu as d', 'd.idU', 'u.idU')
            ->where('t.tipo', 3)->where('t.estadodt', 2)
            ->whereRaw('DATE(t.created_at) = ?', [$fecha])
            ->select('cu.idU', 'd.nomU', 'd.apU')
            ->selectRaw('ROUND(SUM(t.total),2) as cobrado, ROUND(SUM(t.cuota),2) as cuota, ROUND(SUM(t.mora),2) as mora, COUNT(*) as n_cobros')
            ->groupBy('cu.idU')->orderByDesc('cobrado');
        if ($idU) $q->where('cu.idU', $idU);
        $rows = $q->get()->map(fn($r) => (array) $r)->all();
        $tot = ['cobrado' => 0, 'cuota' => 0, 'mora' => 0, 'n_cobros' => 0];
        foreach ($rows as $r) {
            foreach (array_keys($tot) as $k) $tot[$k] += (float) $r[$k];
        }
        $tot['cobrado'] = round($tot['cobrado'], 2);
        $tot['cuota'] = round($tot['cuota'], 2);
        $tot['mora'] = round($tot['mora'], 2);
        return ['fecha' => $fecha, 'usuarios' => $rows, 'total' => $tot];
    }

    public static function guardarSnapshot(string $fecha, ?int $idU = null): int
    {
        global $capsule;
        $av = self::delDia($fecha, $idU);
        $n = 0;
        foreach ($av['usuarios'] as $r) {
            $capsule->table('avance_cobro_diario')->updateOrInsert(
                ['fecha' => $fecha, 'idU' => $r['idU']],
                ['cobrado' => $r['cobrado'], 'cuota' => $r['cuota'], 'mora' => $r['mora'],
                 'n_cobros' => $r['n_cobros'], 'created_at' => date('Y-m-d H:i:s')]
            );
            $n++;
        }
        return $n;
    }

    public static function snapshot(string $fecha): array
    {
        global $capsule;
        return $capsule->table('avance_cobro_diario')->where('fecha', $fecha)->orderByDesc('cobrado')
            ->get()->map(fn($r) => (array) $r)->all();
    }
}

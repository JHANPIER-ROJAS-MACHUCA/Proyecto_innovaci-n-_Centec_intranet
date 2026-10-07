<?php
// Spec 2026 #1b: estadística por sucursal + consolidado general
// (metas, clientes, desembolsos, rentabilidad, morosidad, cartera vencida).
// Vínculo sucursal: tclie_general.idSucursal (créditos/cobros vía cliente),
// usuario_sucursal (personal/metas). Rentabilidad v1 = intereses cobrados /
// desembolsado (aproximación documentada).

class GerenciaService
{
    public static function porSucursal(?int $idS = null): array
    {
        global $capsule;
        $sucs = $capsule->table('sucursales')->where('estado', 1)
            ->when($idS, fn($q) => $q->where('idS', $idS))
            ->orderBy('idS')->get()->map(fn($r) => (array) $r)->all();
        $out = [];
        foreach ($sucs as $s) {
            $out[] = self::bucket((int) $s['idS'], $s['nombre'], $s['codigo'] ?? null);
        }
        // Spec: clientes sin idSucursal NO se reasignan; se contabilizan aparte
        // para no perder información en reportes.
        if ($idS === null) {
            $sin = self::bucket(null, 'Sin sucursal', null);
            if ($sin['clientes'] > 0 || $sin['creditos'] > 0 || $sin['cobrado'] > 0) $out[] = $sin;
        }
        $tot = ['sucursales' => count($out), 'clientes' => 0, 'creditos' => 0, 'desembolsado' => 0, 'cobrado' => 0, 'cartera_vencida' => 0, 'saldo_ahorros' => 0];
        foreach ($out as $r) {
            foreach (['clientes', 'creditos'] as $k) $tot[$k] += $r[$k];
            foreach (['desembolsado', 'cobrado', 'cartera_vencida', 'saldo_ahorros'] as $k) $tot[$k] += $r[$k];
        }
        $tot['desembolsado'] = round($tot['desembolsado'], 2);
        $tot['cobrado'] = round($tot['cobrado'], 2);
        $tot['cartera_vencida'] = round($tot['cartera_vencida'], 2);
        $tot['saldo_ahorros'] = round($tot['saldo_ahorros'], 2);
        return ['sucursales' => $out, 'consolidado' => $tot];
    }

    private static function bucket(?int $idS, string $nombre, ?string $codigo): array
    {
        global $capsule;
        $clis = $idS === null
            ? $capsule->table('tclie_general')->whereNull('idSucursal')->pluck('idCG')
            : $capsule->table('tclie_general')->where('idSucursal', $idS)->pluck('idCG');
        $nClis = count($clis);
        $cred = $capsule->table('tprestamo')->whereIn('idCG', $clis ?: [0])
            ->selectRaw('COUNT(*) as n, COALESCE(SUM(montoAprovado),0) as aprobado, COALESCE(SUM(CASE WHEN estado=4 THEN capital ELSE 0 END),0) as desembolsado, COALESCE(SUM(capital),0) as desembolsado_hist, COUNT(CASE WHEN estado=4 THEN 1 END) as activos')->first();
        $cobQ = $capsule->table('tcaja_usu_detal as t')->join('tclie_general as c', 't.cliente', 'c.idCG');
        if ($idS === null) $cobQ->whereNull('c.idSucursal'); else $cobQ->where('c.idSucursal', $idS);
        $cob = $cobQ->where('t.tipo', 3)->where('t.estadodt', 2)
            ->selectRaw('COALESCE(SUM(t.total),0) as cobrado, COALESCE(SUM(t.cuota),0) as cuota, COALESCE(SUM(t.mora),0) as mora, COUNT(*) as n')->first();
        $morQ = $capsule->table('tpresta_detalle as d')->join('tprestamo as p', 'd.idP', 'p.idP')
            ->join('tclie_general as c', 'p.idCG', 'c.idCG');
        if ($idS === null) $morQ->whereNull('c.idSucursal'); else $morQ->where('c.idSucursal', $idS);
        $mor = $morQ->where('p.estado', 4)->where('d.is_finished', 0)
            ->whereRaw('COALESCE(d.fechaProg, d.expiration_at) < CURDATE()')
            ->selectRaw('COUNT(*) as cuotas_vencidas, COALESCE(SUM(COALESCE(d.cuota, d.capital + d.interest, 0) - COALESCE(d.montoPagado, d.capital_payment + d.interest_payment, 0)),0) as cartera_vencida')->first();
        $ahQ = $capsule->table('tahorro_deta as d')->join('tahorro as a', 'd.idA', 'a.idA')
            ->join('tclie_general as c', 'a.id', 'c.idCG');
        if ($idS === null) $ahQ->whereNull('c.idSucursal'); else $ahQ->where('c.idSucursal', $idS);
        $ah = $ahQ->selectRaw('COALESCE(SUM(CASE WHEN d.tipo=7 THEN d.monto ELSE 0 END),0) - COALESCE(SUM(CASE WHEN d.tipo=8 THEN d.monto ELSE 0 END),0) as saldo_ahorros')->first();
        $ases = $idS === null ? 0 : (int) $capsule->table('usuario_sucursal as us')->join('tusuarios as u', 'u.idU', 'us.idU')
            ->where('us.idS', $idS)->where('us.estado', 1)->where('u.idEstado', 1)->count();
        $metas = $idS === null ? 0 : (int) $capsule->table('goals as g')->join('usuario_sucursal as us', 'us.idU', 'g.user_id')
            ->where('us.idS', $idS)->where('us.estado', 1)->count();
        $des = (float) ($cred->desembolsado ?? 0);
        $desH = (float) ($cred->desembolsado_hist ?? 0);
        $cobT = (float) ($cob->cobrado ?? 0);
        return [
            'idS' => $idS, 'nombre' => $nombre, 'codigo' => $codigo,
            'clientes' => $nClis, 'personal_activo' => $ases, 'metas' => $metas,
            'creditos' => (int) ($cred->n ?? 0), 'creditos_activos' => (int) ($cred->activos ?? 0),
            'aprobado' => (float) ($cred->aprobado ?? 0), 'desembolsado' => $des,
            'cobrado' => $cobT, 'n_cobros' => (int) ($cob->n ?? 0),
            'cuotas_vencidas' => (int) ($mor->cuotas_vencidas ?? 0),
            'morosidad_monto' => (float) ($mor->cartera_vencida ?? 0),
            'cartera_vencida' => (float) ($mor->cartera_vencida ?? 0),
            'saldo_ahorros' => (float) ($ah->saldo_ahorros ?? 0),
            'rentabilidad' => $desH > 0 ? round(($cobT - $desH) / $desH * 100, 2) : 0,
        ];
    }
}

class GerenciaController
{
    public static function sucursales(AppRequest $req): void
    {
        RoleMiddleware::require([8, 1]);
        $idS = isset($req->query['idS']) && $req->query['idS'] !== '' ? (int) $req->query['idS'] : null;
        Response::json(['data' => GerenciaService::porSucursal($idS), 'success' => true]);
    }
}

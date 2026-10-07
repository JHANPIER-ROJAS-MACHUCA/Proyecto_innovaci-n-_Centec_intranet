<?php
// Clasificación AUTOMÁTICA por comportamiento de pago (spec 2026 v2).
// Puntaje 0-100 desde el HISTORIAL COMPLETO (tpresta_detalle), combinando:
//   1) puntualidad (40): % cuotas exigibles pagadas a tiempo o antes
//   2) moras (25): tasa de cuotas con mora cobrada (pagoMora>0 o estado_mora=1)
//   3) días de atraso (20): promedio de días de retraso neto
//   4) evolución (15): puntualidad mitad reciente vs mitad antigua + recencia
// Fuentes de días: delay_payment - delay_condoned (resueltas) o
// hoy - vencimiento (impagas vencidas). Exigible = vencimiento <= hoy.
// Bandas: A ≥85 (premium), B 70-84 (bueno), C 50-69 (riesgoso), D <50 (crítico).
// Cada clase tiene lógica DISTINTA (D ≠ A). Sin historial exigible → sin clase.
// Dinámica: se recalcula en vivo en cada consulta (mejora/empeora con pagos).
// La columna tclie_general.sentinel queda como dato referencial (carga manual),
// NO define la clase.

class ClasificacionService
{
    public const UMBRAL_A = 85;
    public const UMBRAL_B = 70;
    public const UMBRAL_C = 50;

    // Métricas de comportamiento sobre todo el historial
    public static function historial(int $idCG): array
    {
        global $capsule;
        $rows = $capsule->table('tpresta_detalle as d')->join('tprestamo as p', 'd.idP', 'p.idP')
            ->where('p.idCG', $idCG)
            ->whereRaw('COALESCE(d.fechaProg, d.expiration_at) IS NOT NULL')
            ->whereRaw('COALESCE(d.fechaProg, d.expiration_at) <= CURDATE()')
            ->select('d.idPD', 'd.cuota', 'd.montoPagado', 'd.pagoMora', 'd.estado_mora', 'd.delay_payment', 'd.delay_condoned', 'd.is_finished')
            ->selectRaw('COALESCE(d.fechaProg, d.expiration_at) as vencimiento')
            ->selectRaw('COALESCE(d.fechaPago, d.payment_date) as pagada')
            ->orderBy('vencimiento')->orderBy('d.idPD')
            ->get()->map(fn($r) => (array) $r)->all();

        $m = ['n_exp' => 0, 'n_punt' => 0, 'n_ret' => 0, 'n_moras' => 0, 'dias_tot' => 0, 'dias_max' => 0, 'ultimo_retraso' => null];
        $seq = [];
        foreach ($rows as $r) {
            $cond = max(0, (int) ($r['delay_condoned'] ?? 0));
            $pagada = !empty($r['pagada']) || (int) ($r['is_finished'] ?? 0) === 1;
            if ($pagada) {
                $dias = max(0, (int) ($r['delay_payment'] ?? 0) - $cond);
            } else {
                $dias = max(0, (int) round((time() - strtotime($r['vencimiento'])) / 86400));
            }
            $puntual = $pagada && $dias === 0;
            $mora = ((float) ($r['pagoMora'] ?? 0) > 0) || (($r['estado_mora'] ?? '') === '1');
            $m['n_exp']++;
            if ($puntual) $m['n_punt']++;
            else {
                $m['n_ret']++;
                $m['dias_tot'] += $dias;
                if ($dias > $m['dias_max']) $m['dias_max'] = $dias;
                $m['ultimo_retraso'] = $r['vencimiento'];
            }
            if ($mora) $m['n_moras']++;
            $seq[] = $puntual ? 1 : 0;
        }
        // Evolución: puntualidad mitad reciente vs mitad antigua
        $m['tendencia'] = 0.0;
        $n = count($seq);
        if ($n >= 4) {
            $mit = (int) floor($n / 2);
            $old = array_sum(array_slice($seq, 0, $mit)) / max(1, $mit);
            $rec = array_sum(array_slice($seq, $mit)) / max(1, $n - $mit);
            $m['tendencia'] = round($rec - $old, 3);
        }
        $m['punt_pct'] = $m['n_exp'] > 0 ? round($m['n_punt'] / $m['n_exp'] * 100, 1) : null;
        $m['dias_prom'] = $m['n_ret'] > 0 ? round($m['dias_tot'] / $m['n_ret'], 1) : 0;
        return $m;
    }

    // Puntaje 0-100 con componentes explicables
    public static function puntaje(array $m): array
    {
        if ($m['n_exp'] === 0) {
            return ['puntaje' => null, 'clase' => null, 'detalle' => $m + ['motivo' => 'Sin historial exigible']];
        }
        $pPunt = 40 * $m['n_punt'] / $m['n_exp'];
        $tasaMora = $m['n_moras'] / $m['n_exp'];
        $pMora = 25 * max(0, 1 - $tasaMora * 4);
        $pDias = 20 * max(0, 1 - $m['dias_prom'] / 30);
        $pEvol = $m['n_exp'] >= 4 ? 15 * (0.5 + 0.5 * max(-1, min(1, $m['tendencia']))) : 7.5;
        $score = round(max(0, min(100, $pPunt + $pMora + $pDias + $pEvol)), 1);
        $clase = $score >= self::UMBRAL_A ? 'A' : ($score >= self::UMBRAL_B ? 'B' : ($score >= self::UMBRAL_C ? 'C' : 'D'));
        return ['puntaje' => $score, 'clase' => $clase, 'detalle' => $m + [
            'puntualidad' => round($pPunt, 1), 'moras' => round($pMora, 1),
            'dias' => round($pDias, 1), 'evolucion' => round($pEvol, 1),
        ]];
    }

    public static function deCliente(int $idCG): array
    {
        $m = self::historial($idCG);
        return self::puntaje($m);
    }

    public static function clasificar(?string $categoria = null, int $limit = 500): array
    {
        global $capsule;
        $filtrado = $categoria && $categoria !== 'todas';
        // Con filtro de clase se escanea todo (la tabla es pequeña) para no
        // perder coincidencias; sin filtro se respeta el límite.
        $scan = $filtrado ? 10000 : $limit;
        $clientes = $capsule->table('tclie_general as c')
            ->leftJoin('sucursales as s', 's.idS', 'c.idSucursal')
            ->select('c.idCG', 'c.dni', 'c.nom', 'c.ap', 'c.am', 'c.cel', 'c.sentinel', 'c.idSucursal')
            ->selectRaw("COALESCE(s.nombre, 'Sin sucursal') as sucursal_nombre")
            ->orderByDesc('c.idCG')->limit($scan)->get();
        $out = [];
        foreach ($clientes as $c) {
            $c = (array) $c;
            $r = self::puntaje(self::historial((int) $c['idCG']));
            $c['puntaje'] = $r['puntaje'];
            $c['clase'] = $r['clase'];
            $c['detalle'] = $r['detalle'];
            if ($filtrado && $c['clase'] !== $categoria) continue;
            $out[] = $c;
            if (count($out) >= $limit) break;
        }
        return $out;
    }

    public static function setSentinel(int $idCG, string $valor): void
    {
        global $capsule;
        $valor = strtoupper(trim($valor));
        if (!in_array($valor, ['NORMAL', 'MEDIANO RIESGO', 'ALTO RIESGO'], true)) {
            throw new DomainException('Sentinel válido: NORMAL, MEDIANO RIESGO, ALTO RIESGO.');
        }
        $n = $capsule->table('tclie_general')->where('idCG', $idCG)->update(['sentinel' => $valor]);
        if (!$n) throw new DomainException('Cliente no existe.');
    }

    // Cuotas retrasadas + días agrupados por cliente (base de cartas cobranza)
    public static function morosidad(): array
    {
        global $capsule;
        return $capsule->table('tpresta_detalle as d')
            ->join('tprestamo as p', 'd.idP', 'p.idP')
            ->join('tclie_general as c', 'p.idCG', 'c.idCG')
            ->leftJoin('sucursales as s', 's.idS', 'c.idSucursal')
            ->where('p.estado', 4)->where('d.is_finished', 0)
            ->whereRaw('COALESCE(d.fechaProg, d.expiration_at) < CURDATE()')
            ->select('c.idCG', 'c.dni', 'c.nom', 'c.ap', 'c.am', 'c.cel', 'c.idSucursal')
            ->selectRaw("COALESCE(s.nombre, 'Sin sucursal') as sucursal_nombre")
            ->selectRaw('COUNT(*) as cuotas_retrasadas')
            ->selectRaw('SUM(DATEDIFF(CURDATE(), COALESCE(d.fechaProg, d.expiration_at))) as dias_total')
            ->selectRaw('MAX(DATEDIFF(CURDATE(), COALESCE(d.fechaProg, d.expiration_at))) as dias_max')
            ->selectRaw('ROUND(SUM(COALESCE(d.cuota, d.capital + d.interest, 0) - COALESCE(d.montoPagado, d.capital_payment + d.interest_payment, 0)), 2) as deuda')
            ->groupBy('c.idCG')->orderByDesc('dias_max')->limit(500)
            ->get()->map(fn($r) => (array) $r)->all();
    }

    public static function tramo(int $diasMax): string
    {
        if ($diasMax <= 8) return '1-8 días';
        if ($diasMax <= 30) return '9-30 días';
        if ($diasMax <= 60) return '31-60 días';
        return '60+ días';
    }

    // Compatibilidad: conteos usados por reportes antiguos
    public static function retrasosUltimos5(int $idCG): int
    {
        return self::historial($idCG)['n_ret'];
    }

    public static function nCredidiarios(int $idCG): int
    {
        global $capsule;
        return (int) $capsule->table('tprestamo')->where('idCG', $idCG)->where('payment_period', 'daily')->count();
    }
}

class CartaService
{
    // Invitación pre-aprobada: solo clase A del motor de comportamiento
    public static function invitacion(int $idCG): array
    {
        $c = ClasificacionService::clasificar('A', 5000);
        $cli = null;
        foreach ($c as $x) {
            if ((int) $x['idCG'] === $idCG) { $cli = $x; break; }
        }
        if (!$cli) throw new DomainException('Cliente no califica como A (invitación pre-aprobada).');
        $nom = trim(($cli['nom'] ?? '') . ' ' . ($cli['ap'] ?? '') . ' ' . ($cli['am'] ?? ''));
        return [
            'titulo' => 'Carta de invitación — crédito pre-aprobado (Clase A)',
            'contenido' => "Estimado(a) $nom (DNI {$cli['dni']}): por su excelente comportamiento de pago (puntaje {$cli['puntaje']}/100), tiene un CRÉDITO PRE-APROBADO. Acérquese a su agencia para hacerlo efectivo.",
            'cliente' => $cli,
        ];
    }

    public static function cobranza(int $idCG): array
    {
        $mora = ClasificacionService::morosidad();
        $row = null;
        foreach ($mora as $x) {
            if ((int) $x['idCG'] === $idCG) { $row = $x; break; }
        }
        if (!$row) throw new DomainException('Cliente sin cuotas retrasadas.');
        $tramo = ClasificacionService::tramo((int) $row['dias_max']);
        $nom = trim(($row['nom'] ?? '') . ' ' . ($row['ap'] ?? '') . ' ' . ($row['am'] ?? ''));
        return [
            'titulo' => "Carta de cobranza — tramo $tramo",
            'tramo' => $tramo,
            'contenido' => "Estimado(a) $nom (DNI {$row['dni']}): registra {$row['cuotas_retrasadas']} cuota(s) retrasada(s), con un máximo de {$row['dias_max']} días de atraso y deuda de S/ {$row['deuda']} (tramo $tramo). Regularice a la brevedad para evitar cargos adicionales.",
            'cliente' => $row,
        ];
    }

    public static function registrar(int $idCG, string $tipo, ?string $tramo, string $titulo, string $contenido, int $idU): int
    {
        global $capsule;
        if (!in_array($tipo, ['invitacion', 'cobranza'], true)) throw new DomainException('Tipo inválido.');
        return $capsule->table('carta_cobranza')->insertGetId([
            'idCG' => $idCG, 'tipo' => $tipo, 'tramo' => $tramo, 'titulo' => $titulo,
            'contenido' => $contenido, 'idU' => $idU, 'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function historial(int $idCG): array
    {
        global $capsule;
        return $capsule->table('carta_cobranza')->where('idCG', $idCG)->orderByDesc('id')->limit(50)
            ->get()->map(fn($r) => (array) $r)->all();
    }
}

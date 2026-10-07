<?php
class CajaRepository
{
    public static function habilitada(array $user): ?object
    {
        global $capsule;
        if ((int)($user['tipoU'] ?? 0) === 5) { // admin_personal (antes tipoU 2)
            $oficina = $capsule->table('tcaja_oficina')->select('idCO')
                ->where('idO', $user['idO'])->orderBy('idCO', 'desc')->first();
            if (!$oficina) return null;
            return $capsule->table('tcaja_usuario')->where('idCO', $oficina->idCO)->orderBy('idCA')->first();
        }
        return $capsule->table('tcaja_usuario')->where('idU', $user['idU'])
            ->whereNull('montoFin')->orderBy('idCA', 'desc')->first();
    }

    public static function resumen(object $caja): array
    {
        global $capsule;
        $efectivo = 0.0;
        $digital = 0.0;
        if (($caja->tipo ?? null) == 1) {
            $otras = (float) $capsule->table('tcaja_usuario')->where('idCO', $caja->idCO)->where('idCA', '!=', $caja->idCA)->sum('montoFin');
            $desig = (float) \App\Models\Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
                ->where('tcaja_usuario.idCO', $caja->idCO)->where('tcaja_usu_detal.tipo', '1')
                ->where('tcaja_usu_detal.habilitacion', '4')->sum('tcaja_usu_detal.monto');
            $efectivo += ($caja->monto ?? 0) + $otras - $desig;
        }
        $efectivo += (float) \App\Models\Transaction::where('idCA', $caja->idCA)->where('tipo', 1)->where('estadodt', 2)->where('habilitacion', 4)->sum('monto');
        $cobros = \App\Models\Transaction::leftJoin('transaction_details', 'tcaja_usu_detal.idCAD', 'transaction_details.transaction_id')
            ->where('tcaja_usu_detal.idCA', $caja->idCA)->where('tcaja_usu_detal.estadodt', 2)->where('tcaja_usu_detal.tipo', 3)
            ->selectRaw('sum(if(transaction_details.id, tcaja_usu_detal.total,0)) as digital')
            ->selectRaw('sum(if(transaction_details.id, 0,tcaja_usu_detal.total)) as cash')->first();
        $efectivo += (float) ($cobros->cash ?? 0);
        $digital += (float) ($cobros->digital ?? 0);
        $ahorro = \App\Models\Transaction::join('tahorro_deta', 'tcaja_usu_detal.idCuota', 'tahorro_deta.idAd')
            ->where('tcaja_usu_detal.idCA', $caja->idCA)->where('tcaja_usu_detal.estadodt', 2)
            ->selectRaw('sum(if(tahorro_deta.tipo = 7, tcaja_usu_detal.total, 0)) as income')
            ->selectRaw('sum(if(tahorro_deta.tipo = 8, tcaja_usu_detal.total, 0)) as expense')->first();
        $efectivo += (float) ($ahorro->income ?? 0) - (float) ($ahorro->expense ?? 0);
        $mov = \App\Models\Transaction::join('tahorro_motivo', 'tcaja_usu_detal.tipo', 'tahorro_motivo.idam')
            ->where('tcaja_usu_detal.idCA', $caja->idCA)->where('tcaja_usu_detal.estadodt', 2)
            ->whereNotIn('tcaja_usu_detal.tipo', [1, 2, 3])->whereNull('tcaja_usu_detal.idCuota')
            ->selectRaw('sum(if(tahorro_motivo.tipoM = 1, tcaja_usu_detal.total, 0)) as income')
            ->selectRaw('sum(if(tahorro_motivo.tipoM = 2, tcaja_usu_detal.total, 0)) as expense')->first();
        $efectivo += (float) ($mov->income ?? 0) - (float) ($mov->expense ?? 0);
        $efectivo -= (float) \App\Models\Transaction::where('tcaja_usu_detal.idCA', $caja->idCA)->where('tcaja_usu_detal.estadodt', 2)->where('tcaja_usu_detal.tipo', 2)->sum('total');
        return ['efectivo' => round($efectivo, 2), 'digital' => round($digital, 2)];
    }

    public static function abrirGerencia(int $idU): int
    {
        global $capsule;
        $abierta = $capsule->table('tcaja_oficina')->where('idO', 'CG')->whereNull('montof')->orderBy('idCO')->first();
        if ($abierta) throw new DomainException('Gerencia ya abrió caja.');
        $ultima = $capsule->table('tcaja_oficina')->where('idO', '!=', 'CG')->whereNotNull('montof')->orderBy('idCO', 'desc')->first();
        $id = $capsule->table('tcaja_oficina')->insertGetId([
            'ini' => date('Y-m-d'), 'iniH' => date('H:i:s'),
            'monto' => $ultima ? $ultima->monto : 0, 'idR' => $idU, 'idO' => 'CG',
        ]);
        $capsule->table('tbilletaje')->truncate();
        return $id;
    }

    public static function abrirOficina(array $user): int
    {
        global $capsule;
        $gerencia = $capsule->table('tcaja_oficina')->where('idO', 'CG')->whereNull('fin')->orderBy('idCO', 'desc')->first();
        if (!$gerencia) throw new DomainException('Gerencia aún no abre caja.');
        $existe = $capsule->table('tcaja_oficina')->where('idO', $user['idO'])->where('ini', $gerencia->ini)->first();
        if ($existe) throw new DomainException('La oficina ya abrió caja hoy.');
        $anterior = $capsule->table('tcaja_oficina')->where('idO', $user['idO'])->orderBy('idCO', 'desc')->first();
        $saldo = 0;
        if ($anterior) $saldo = $anterior->montof !== '' && $anterior->montof !== null ? (float) $anterior->montof : (float) $anterior->monto;
        $bodega = (float) $capsule->table('tcaja_bodega')->where('estado', '2')->where('estadoConsumo', '1')->where('idO', $user['idO'])->sum('monto');
        return $capsule->table('tcaja_oficina')->insertGetId([
            'ini' => $gerencia->ini, 'iniH' => date('H:i:s'),
            'monto' => round($saldo + $bodega, 2), 'idR' => $user['idU'], 'idO' => $user['idO'],
        ]);
    }

    public static function cerrar(int $idU, $monto): void
    {
        global $capsule;
        $ultima = $capsule->table('tcaja_usuario')->where('idU', $idU)->orderBy('idCA', 'desc')->first();
        if (!$ultima) throw new DomainException('Sin caja para cerrar.');
        $capsule->table('tcaja_usuario')->where('idCA', $ultima->idCA)->update(['montofin' => $monto, 'hfin' => date('H:i:s')]);
    }
}
